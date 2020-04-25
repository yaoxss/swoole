### 什么是swoole
    swoole是一个为PHP用C和C++编写的基于事件的高性能异步和协程并行网络通信引擎(客户端与服务端)
    
### 通道
    通道(Channel)是协程之间通信交换数据的唯一渠道，而协程+通道的开发组合即为著名的CSP编程模型。
    在swoole开发中，channel常用于连接池的实现和协程并发的调度。
    
### Swoole的三大功能
    常驻进程
    异步
    协程
    协程能极大的提高并发，但是有时候并发高了不一定是好事(最理想的并发是能处理多少并发就来多少并发)
    
### 如何正确的搭建ＨＴＴＰ服务

### 安装ab(apache benchmark)
    yum -y install httpd-tools
    测试：ab -help
    使用方法：
    ab -n 800 -c 800 http://127.0.0.1:9501/
    (-n　发出８００个请求，－ｃ模拟８００个并发，相当于８００人同时访问，后面是测试ｕｒｌ)
    ab -t 60 -c 100 http://127.0.0.1:9501/
    (在６０秒内发请求，一次１００个请求)
    ab -c 100 -n 10000 http://127.0.0.1:9501/
    (-c　１００　即：每次并发１００个
    －ｎ　１００００　)
    [junjie2@login htdocs]$ /data1/apache/bin/ab -c 1000 -n 50000 "http://10.10.10.10/a.php "
    This is ApacheBench, Version 1.3d <$Revision: 1.73 $> apache-1.3
    Copyright (c) 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/ 
    Copyright (c) 1998-2002 The Apache Software Foundation, http://www.apache.org/
    
    Benchmarking 10.65.129.21 (be patient)
    Completed 5000 requests
    Completed 10000 requests
    Completed 15000 requests
    Completed 20000 requests
    Completed 25000 requests
    Completed 30000 requests
    Completed 35000 requests
    Completed 40000 requests
    Completed 45000 requests
    Finished 50000 requests
    Server Software: Apache/1.3.33 
    Server Hostname: 10.65.129.21
    Server Port: 80
    
    Document Path: /a.php //请求的资源
    Document Length: 0 bytes // 文档返回的长度，不包括相应头
    
    Concurrency Level: 1000 // 并发个数
    Time taken for tests: 48.650 seconds //总请求时间 
    Complete requests: 50000 // 总请求数
    Failed requests: 0 //失败的请求数
    Broken pipe errors: 0
    Total transferred: 9750000 bytes
    HTML transferred: 0 bytes
    Requests per second: 1027.75 [#/sec] (mean) // 平均每秒的请求数
    Time per request: 973.00 [ms] (mean) // 平均每个请求消耗的时间
    Time per request: 0.97 [ms] (mean, across all concurrent requests) // 就是上面的时间 除以并发数
    Transfer rate: 200.41 [Kbytes/sec] received // 时间传输速率
    
    Connnection Times (ms)
    min mean[+/-sd] median max
    Connect: 0 183 2063.3 0 45003
    Processing: 28 167 770.6 85 25579
    Waiting: 21 167 770.6 85 25578
    Total: 28 350 2488.8 85 48639
    
    Percentage of the requests served within a certain time (ms)
    50% 85 // 就是有50%的请求都是在85ms内完成的
    66% 89
    75% 92
    80% 96
    90% 168
    95% 640
    98% 984
    99% 3203
    100% 48639 (last request)