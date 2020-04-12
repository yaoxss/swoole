### 什么是TCP协议
    TCP（Transmission Control Protocol 传输控制协议）是一种面向连接的、可靠的、基于字节流的传输层通信协议
    
### 握手常见问题
    1、连接拒绝
    2、Operation now in progress
        丢包、错误ip、backlog满了&阻塞&tcp_abort_on_overflow=0
    3、min(maxconn,backlog) ss -lt
    
### backlog
    OnConnect 回调之后的连接队列
    
### SYN Flood
    