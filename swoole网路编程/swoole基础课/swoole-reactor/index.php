<?php
$http = new swoole_http_server('0.0.0.0', 9501);

$http->on('request', function ($request, $response) {
    $redis = new Swoole\Coroutine\Redis();
    $redis->connect('127.0.0.1', 6379);
    $result = $redis->get('key');
    $result = $redis->get($result);
    $result = $redis->get($result);
    $result = $redis->get($result);
    var_dump("终于结束了",$result);
});

//$http->on('request', function ($request, $response) {
//    go(function (){
//        $redis = new Swoole\Coroutine\Redis();
//        $redis->connect('127.0.0.1', 6379);
//        $result = $redis->get('key');
//        $result = $redis->get($result);
//        $result = $redis->get($result);
//        $result = $redis->get($result);
//        var_dump("终于结束了",$result);
//    });
//});