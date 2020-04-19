<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020\4\15 0015
 * Time: 21:16
 */

class Pool{
    // 单例模式
    protected static $instance;
    public static function i():self
    {
        return static::$instance ?? (static::$instance = new static());
    }

    // 通过$channel来调用连接池
    protected $channel;

    public function __construct(int $size = 100){
        // 通道，用于协程间通讯，支持多生产者协程和多消费者协程。底层自动实现了协程的切换和调度。
        $this->channel = new Swoole\Coroutine\Channel($size);

        while($size--) {
            $redis = new Swoole\Coroutine\Redis;
            $ret = $redis->connect('127.0.0.1', 6379);
            if($ret === true){
                $this->put($redis);
            }else{
                Co::sleep(0.1);
                throw new RangeException($redis->errMsg,$redis->errCode);
            }
        }
    }

// 调用不存在的方法时会调用该方法
// ，第一个参数 $function_name 会自动接收不存在的方法名，第二个 $args 则以数组的方式接收不存在方法的多个参数。
//    public function __call(string $name, ...$args)
//    {
//        $redis = $this->get();
//        $value = $redis->$name(...$args);
//        if ($value === false) {
//            throw new RangeException($redis->errMsg, $redis->errCode);
//        }
//        $redis->put($redis);
//        return $value;
--with-php-config /www/server/php/74/src/scripts/php-config


//    }

    public function put(Swoole\Coroutine\Redis $redis) : void
    {
        // 向通道中写入数据。
        $this->channel->push($redis);
    }

    // $timeout 设置超时时间
    public function get(float $timeout): Swoole\Coroutine\Redis
    {
        // 从通道中读取数据
        // 多个生产者协程同时 push 时，底层自动进行排队，按照顺序逐个 resume 这些生产者协程
        return $this->channel->pop($timeout) ?: null;
    }

    public function close(){
//        $this->channel->close();
        $this->channel = null;
    }
}

//$value = Pool::i()->get('foo');
//var_dump($value);

$count = 0;
// 相当于入口函数
Co\run(function (){
    Pool::i();
    for($c = 1000; $c--;){
        // (function(){})();
        go(function(){
            $pool = Pool::i();
            $redis = $pool->get(100);
            defer(function () use ($pool, $redis){
                $pool->put($redis);
            });
            $value = $redis->get('foo');
            if($value === false){
                throw new RangeException($redis->errMsg, $redis->errCode);
            }
            global $count;
            if (assert($value === 'bar')) {
                $count++;
            }

            // 结束的时候调用defer函数
        });
    }
});

var_dump($count);