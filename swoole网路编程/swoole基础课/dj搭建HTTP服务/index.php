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
//        'hook_flags' => SWOOLE_HOOK_ALL
    ]);

    // 注册事件回调函数
    $server->on('workerStart', function () use($process, $server){
        // 向管道写入数据
        $process->write('1');
    });

    //request 在收到一个完整的 HTTP 请求后，会回调此函数。回调函数共有 2 个参数：
    $server->on('request', function (Request $request, Response $response) use($server){
        try{
            $redis = new Redis;
            $redis->connect('127.0.0.1', 6379);
            $greeter = $redis->get('key');
            if(!$greeter){
                throw new RedisException('get data failed');
            }
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


//[root@iZ2zebn3hsgwulgeqhZ dj搭建HTTP服务]# php index.php
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
//Time taken for tests:   2.202 seconds
//Complete requests:      10000
//Failed requests:        0
//Write errors:           0
//Keep-Alive requests:    10000
//Total transferred:      1650000 bytes
//HTML transferred:       120000 bytes
//Requests per second:    4542.09 [#/sec] (mean)
//        Time per request:       56.362 [ms] (mean)
//Time per request:       0.220 [ms] (mean, across all concurrent requests)
//Transfer rate:          731.88 [Kbytes/sec] received
//
//Connection Times (ms)
//              min  mean[+/-sd] median   max
//Connect:        0    3  51.9      0    1001
//Processing:     6   53  21.6     50     228
//Waiting:        1   53  21.6     50     228
//Total:          6   56  60.1     50    1141
//
//Percentage of the requests served within a certain time (ms)
//  50%     50
//  66%     51
//  75%     52
//  80%     53
//  90%     54
//  95%     62
//  98%    174
//  99%    176
// 100%   1141 (longest request)

