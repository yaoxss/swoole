<?php
// 压测脚本
use Swoole\Http\Request;
use Swoole\Http\Response;

$process = new Swoole\Process(function () {
    $server = new Swoole\Http\Server('127.0.0.1',9501,SWOOLE_BASE);
    $server->set([
        'log_file' => '/dev/null',
        'log_level' => SWOOLE_LOG_INFO,
        'worker_rum' => swoole_cpu_num() * 2,
//        'hook_flags' => SWOOLE_HOOK_ALL
    ]);

    $server->on('workerStart', function () use($process, $server){
        $process->write('1');
    });

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
    $server->start();
});
if($process->start()){
    register_shutdown_function(function () use ($process) {
        $process::kill($process->pid);
        $process::wait();
    });
    $process->read(1);
    System('ab -c 256 -n 10000 -k http://127.0.0.1:9501/ 2>&1');
}

