<?php

$socket = new CO\Socket(AF_INET, SOCK_STREAM, 0);

go(function () use ($socket){
    $socket->connect('127.0.0.1', 6666);
    // 解决阻塞问题，存在的问题是必须要接收方接收到这个数据
    $socket->setOption(SOL_SOCKET, SO_LINGER, ['l_onoff' =>1, 'l_linger' => 0]);

    $i = 0;
    while($i++ < 1000){
        // str_repeat() 函数把字符串重复指定的次数。
        var_dump($socket->send(str_repeat("1",1024)));
    }
});