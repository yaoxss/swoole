<?php
// 压测脚本
use Swoole\Http\Request;
use Swoole\Http\Response;

// swoole提供的进程管理模块， 子进程创建成功后要执行的函数。
$process = new Swoole\Process(function (Swoole\Process $process) {
    // 创建一个HTTP服务器
    $server = new Swoole\Http\Server('127.0.0.1',9501,SWOOLE_BASE);
    $server->set([
        'log_file' => '/dev/null',
        'log_level' => SWOOLE_LOG_INFO,
        'worker_rum' => swoole_cpu_num() * 2,
        // 将所有同步堵塞的方法，替换成异步非堵塞的协程调度
        'hook_flags' => SWOOLE_HOOK_ALL
    ]);

    // 注册事件回调函数
    $server->on('workerStart', function () use($process, $server){
//        $server->pool = new RedisQueue();
        $server->pool = new RedisPool(64);
        // 向管道写入数据
        $process->write('1');
    });

    //request 在收到一个完整的 HTTP 请求后，会回调此函数。回调函数共有 2 个参数：
    $server->on('request', function (Request $request, Response $response) use($server){
        try{
            // RedisPool 在使用过程中发现没有连接的时候，就会把这个协程挂起(让出了控制权，让别的连接继续运行)
            $redis = $server->pool->get();
//            $redis = new Redis;
//            $redis->connect('127.0.0.1', 6379);
            $greeter = $redis->get('key');
            if(!$greeter){
                throw new RedisException('get data failed');
            }
            $server->pool->put($redis);
            $response->end("<h1>{$greeter}</h1>");
        }catch(Throwable $throwable){
            $response->status(500);
            $response->end();
        }
    });
    // 启动 HTTP 服务器
    $server->start();
});

// $process->start() 执行 fork 系统调用，启动子进程。
if($process->start()){
    register_shutdown_function(function () use ($process) {
        // 向指定 pid 进程发送信号。
        $process::kill($process->pid);
        $process::wait();
    });
    // 回收结束运行的子进程。
    $process->read(1);
    System('ab -c 256 -n 10000 -k http://127.0.0.1:9501/');
}

class RedisQueue{
    protected $pool;

    public function __construct()
    {
        // 创建一个队列
        $this->pool = new SplQueue();
    }

    public function get(): Redis
    {
        // 队列等于null的时候创建一个连接
        if($this->pool->isEmpty()){
            $redis = new \Redis();
            $redis->connect('127.0.0.1',6379);
            return $redis;
        }
        return $this->pool->dequeue();
    }

    public function put(Redis $redis){
        $this->pool->dequeue($redis);
    }

    public function close(): void
    {
        $this->pool = null;
    }
}

class RedisPool{
    protected $pool;

    public function __construct(int $size = 100)
    {
        $this->pool = new \Swoole\Coroutine\Channel($size);
        // 在一开始的时候创建redis连接数
        for ($i = 0; $i < $size; $i++) {
            while (true) {
                try{
                    $redis = new \Redis();
                    $redis->connect('127.0.0.1',6379);
                    $this->put($redis);
                    break;
                }catch(\Throwable $throwable){
                    usleep(1 * 1000);
                    continue;
                }
            }
        }
    }

    public function get(): \Redis
    {
        return $this->pool->pop();
    }

    public function put(\Redis $redis){
        $this->pool->push($redis);
    }

    public function close(): void
    {
        $this->pool->close();
        $this->pool = null;
    }
}


//This is ApacheBench, Version 2.3 <$Revision: 1430300 $>
//Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
//Licensed to The Apache Software Foundation, http://www.apache.org/
//
//Benchmarking 127.0.0.1 (be patient)
//Completed 1000 requests
//Completed 2000 requests
//Completed 3000 requests
//Completed 4000 requests
//Completed 5000 requests
//Completed 6000 requests
//Completed 7000 requests
//Completed 8000 requests
//Completed 9000 requests
//Completed 10000 requests
//Finished 10000 requests
//
//
//Server Software:        swoole-http-server
//Server Hostname:        127.0.0.1
//Server Port:            9501
//
//Document Path:          /
//Document Length:        12 bytes
//
//Concurrency Level:      256
//Time taken for tests:   0.724 seconds
//Complete requests:      10000
//Failed requests:        0
//Write errors:           0
//Keep-Alive requests:    10000
//Total transferred:      1650000 bytes
//HTML transferred:       120000 bytes
//Requests per second:    13809.62 [#/sec] (mean)
//        Time per request:       18.538 [ms] (mean)
//Time per request:       0.072 [ms] (mean, across all concurrent requests)
//Transfer rate:          2225.18 [Kbytes/sec] received
//
//Connection Times (ms)
//              min  mean[+/-sd] median   max
//Connect:        0    0   0.8      0       7
//Processing:     7   15   2.4     14      27
//Waiting:        1   15   2.4     14      27
//Total:          8   15   2.7     14      32
//
//Percentage of the requests served within a certain time (ms)
//  50%     14
//  66%     14
//  75%     18
//  80%     18
//  90%     18
//  95%     19
//  98%     21
//  99%     25
// 100%     32 (longest request)


