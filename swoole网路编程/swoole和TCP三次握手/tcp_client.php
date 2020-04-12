<?php
//
$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
var_dump($client->connect('192.168.2.194',6666));

sleep(1000);

/*$i = 0;
while(true){
    $arr[$i] = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
    var_dump($arr[$i]->connect('192.168.2.194', 6666));
    if ($i > 200) {
        var_dump($arr[$i]->recv());
    }
    ++$i;
}*/