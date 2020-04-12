<?php
//

$server = new Swoole\Server("192.168.2.194",6666,SWOOLE_BASE);

$server->set([
    'worker_num' => 1,
    'backlog' => 128
]);

$server->on('connect', function ($server, $fd){
    var_dump("Client:Connect.\n");
    sleep(1000);
});

$server->on('receive', function ($server, $fd, $reactor_id, $data){
    var_dump($data);
});

$server->on('close', function ($server, $fd){
    var_dump("close");
});

$server->start();