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
// netstat -anp|grep 3306|grep EST