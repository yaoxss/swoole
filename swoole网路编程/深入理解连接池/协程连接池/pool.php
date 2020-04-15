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

//    public function __call(string $name, ...$args)
//    {
//        $redis = $this->get();
//        $value = $redis->$name(...$args);
//        if ($value === false) {
//            throw new RangeException($redis->errMsg, $redis->errCode);
//        }
//        $redis->put($redis);
//        return $value;
//    }

    public function put(Swoole\Coroutine\Redis $redis) : void
    {
        $this->channel->push($redis);
    }

    public function get(float $timeout): Swoole\Coroutine\Redis
    {
        return $this->channel->pop($timeout) ?: null;
    }

    public function close(){
//        $this->channel->close();
        $this->channel = null;
    }
}

//$pool = Pool::i();
//$redis = $pool->get();
//$value = $redis->get('foo');
//if($value === false){
//    throw new RangeException($redis->errMsg, $redis->errCode);
//}
//$pool->put($redis);

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
            $redis = $pool->get();
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