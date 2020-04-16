<?php

$pdo = new PDO('mysql:dbname=test;host=127.0.0.1;charset=utf8','root','d435dd6da860e810');

$stmt = $pdo->query("show tables;");
$data = $stmt->fetchAll();
var_dump($data);

// 这里为了模仿，使用了sleep延迟了100s(或者说curl一直得不到回复)。所以前面的连接会一直占用着，没有释放
// 这样子的话，如果说5个进程一起跑就会占用5个连接
sleep(100);

// ps -ef|grep mysql
// mysql的连接占用情况
// netstat -anp|grep 3306|grep php

// 此时会因为sleep(100)秒的原因导致这个连接无法释放掉，他会持续到程序执行完成后才释放掉
//[root@iZ2zebn3hsgwu61wulgeqhZ 进程间的连接池]# netstat -anp|grep 3306|grep php
//tcp        0      0 127.0.0.1:37620         127.0.0.1:3306          ESTABLISHED 20301/php