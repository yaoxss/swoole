### 什么是TCP协议
    TCP（Transmission Control Protocol 传输控制协议）是一种面向连接的、可靠的、基于字节流的传输层通信协议

### TCP的大致执行流程
    客户端与服务端进行三次握手之后，开始互相发送数据，最后进行最后一次握手关闭连接(结束生命周期)
    
### 握手常见问题
    1、连接拒绝
    2、Operation now in progress
        丢包、错误ip、backlog满了&阻塞&tcp_abort_on_overflow=0
    3、min(maxconn,backlog) ss -lt
    
### backlog
    OnConnect 回调之后的连接队列
    
### SYN Flood

### TCP关闭连接的常见问题（time_wait问题持续1分钟之久）
    四元组: 客户端ip、客户端port ----- 服务端ip、服务端po
    Cannot assign requested address (用完端口的时候，客户端报的错)
    Address already in use(设置SO_REUSEADDR避免这个问题，调整net.ipv4.tcp_timestamps=1、net.ipv4.tcp_tw_reuse=1
    、net.ipv4.ip_local_port_range调大,不能开启net.ipv4.tcp_tw_recycle = 1)   
    
### TCP关闭连接的常见问题（close_wait）
    阻塞
    die(其实就是在程序里面使用了die或者程序错误，参数如果是SWOOLE_BASE模式下没有这个问题)  
    

# 短连接的优缺点
### 短连接的性能问题
    １、多余的传输
    ２、ＴＣＰ慢启动问题
    ３、握手阶段丢包
    ４、对连接的占用约等于长连接
###短连接的优点
    １、简单
    ２、理论上连接数会少
    ３、无状态对负载均衡友好     
    
### 关于ＦＰＭ的问题
    ＦＰＭ下所有的连接都是短连接（pconnect除外）
    单例也是短连接 
    