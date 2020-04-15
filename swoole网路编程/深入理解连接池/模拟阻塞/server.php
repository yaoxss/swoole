<?php
//运行客户端时先使用cat /proc/net/sockstat 查看套接字缓冲区使用量(mem)
//[root@iZ2zebn3hsgwu61wulgeqhZ ~]# cat /proc/net/sockstat
//sockets: used 122
//TCP: inuse 11 orphan 0 tw 1 alloc 11 mem 1
//UDP: inuse 3 mem 0
//UDPLITE: inuse 0
//RAW: inuse 0
//FRAG: inuse 0 memory 0
//当客户端发送1000次的时候 执行cat /proc/net/sockstat，再次查看套接字缓冲区使用量(mem)
//[root@iZ2zebn3hsgwu61wulgeqhZ 模拟阻塞]# cat /proc/net/sockstat
//sockets: used 127
//TCP: inuse 15 orphan 1 tw 1 alloc 15 mem 277
//UDP: inuse 2 mem 0
//UDPLITE: inuse 0
//RAW: inuse 0
//FRAG: inuse 0 memory 0
$server = new Swoole\Server("127.0.0.1", 6666, SWOOLE_BASE);
$server->set([
    "worker_num" => 1
]);

// 监听连接进入事件
$server->on('connect', function ($server, $fd){
    echo "进入监听\n";
});

// 监听数据的接收事件
$server->on('receive', function ($server, $fd, $reactor_id, $data){
    echo $data;
    // 模拟阻塞
    sleep(10000);
});

// 监听连接关闭事件
$server->on('close', function ($server, $fd){
    var_dump("close");
});

// 启动服务
$server->start();