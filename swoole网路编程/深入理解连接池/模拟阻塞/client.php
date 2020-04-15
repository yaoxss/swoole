<?php

$socket = new CO\Socket(AF_INET, SOCK_STREAM, 0);

go(function () use ($socket){
    $socket->connect('127.0.0.1', 6666);

    $i = 0;
    while($i++ < 1000){
        // str_repeat() 函数把字符串重复指定的次数。
        var_dump($socket->send(str_repeat("1",1024)));
    }
});