<?php
require __DIR__.'/protocol.php';

use Swoole\Coroutine;
use Swoole\Coroutine\Socket;

Coroutine\run(function () {
    $server = new Socket(AF_INET, SOCK_DGRAM, IPPROTO_IP);
    $server->setProtocol([
        'open_length_check' => true,
        'package_length_type' => 'N',
        'package_length_offset' => 0,
        'package_body_offset' => Protocol::HEAD_LENGTH
    ]);

    if (!$server->bind('127.0.0.1', 6666)) {
        throw new Exception('Bind failed: ' . $server->errMsg);
    }
    if (!$server->listen()) {
        throw new Exception('Bind failed: ' . $server->errMsg);
    }

    while (true) {
        $client = $server->accept(-1);
        if(!$client){
            throw new Exception('Accept failed: ' . $server->errMsg);
        }
        go(function () use ($client) {
            while (true) {
                $packet = $client->recvPacket();
                if (!$packet) {
                    /* Connection closed */
                    break;
                }
                $client->sendAll($packet);
            }
        });
    }
});