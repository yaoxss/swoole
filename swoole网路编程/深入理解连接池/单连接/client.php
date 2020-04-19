<?php
// 自定义协议TCP协议的客户端？
require __DIR__.'/protocol.php';

use Swoole\Coroutine;
use Swoole\Coroutine\Socket;

Coroutine\run(function () {
    $client = new Socket(AF_INET, SOCK_DGRAM, IPPROTO_IP);
    $connected = $client->connect('127.0.0.1', 6666);
    if (!$connected) {
        throw new Exception('Connect failed: '. $client->errMsg);
    }
    $data = Protocol::pack(3, 1, 'Hello Swoole');
    if ($client->sendAll($data) !== strlen($data) ) {
        throw new Exception('Send failed: ' . $client->errMsg);
    }
    $head = $client->recvAll(Protocol::HEAD_LENGTH);
    if ($client->sendAll($head) !== Protocol::HEAD_LENGTH ) {
        throw new Exception('Send failed: ' . $client->errMsg);
    }
    $head = Protocol::unpack($head);
    var_dump($head);

    $length = $head['length'];
    if ($length !== 0) {
        $body = $client->recvAll($length);
        if (strlen($body) !== $length) {
            throw new Exception('Recv head failed: ' . $client->errMsg);
        }
        var_dump($body);
    }
});