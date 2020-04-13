<?php
// 客户端

$client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);

$res = $client->connect('127.0.0.1',6666);

$name = time();

echo "连接状态".$res."\n";

while (true){
    $client->send($name.'每隔1秒向服务器发送-'.data("H:m:s"));
    sleep(5);
}



/*$i = 0;
while(true){
    $arr[$i] = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
    var_dump($arr[$i]->connect('192.168.2.194', 6666));
    if ($i > 200) {
        var_dump($arr[$i]->recv());
    }
    ++$i;
}*/