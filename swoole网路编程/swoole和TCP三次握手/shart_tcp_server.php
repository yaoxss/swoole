<?php
// 服务端

// 创建server对象，监听127.0.0.1:6666端口
$server = new Swoole\Server("127.0.0.1",6666,SWOOLE_BASE);

$server->set([
    'worker_num' => 1,
    'backlog' => 128
]);

// 监听连接进入事件
$server->on('connect', function ($server, $fd){
    echo "进入监听事件";
//    var_dump("Client:Connect.\n");
//    sleep(1000);
});

// 监听数据接收事件
$server->on('receive', function ($server, $fd, $reactor_id, $data){
    var_dump("服务器接收到了你的数据：".$data);
});

// 监听连接关闭事件
$server->on('close', function ($server, $fd){
    var_dump("close");
});

// 启动服务
$server->start();