# PHP Redis 客户端管理器

一个简单的 PHP Redis 客户端连接管理工具


## 安装
~~~
composer require axguowen/redis-client
~~~

## 使用

### 配置连接
~~~php
use \axguowen\facade\RedisClient;

// Redis服务器配置信息设置（全局有效）
RedisClient::setConfig([
    'default' => 'localhost',
    'connections' => [
        // 本机连接参数
        'localhost' => [
            // 主机
            'host'              => '127.0.0.1',
            // 端口
            'port'              => 6379,
            // 密码
            'password'          => '',
            // 数据库索引
            'select'            => 0,
            // 超时时间
            'timeout'           => 0,
            // 是否是长链接
            'persistent'        => false,
            // 部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
            'deploy'            => 0,
            // 读写是否分离 主从式有效
            'rw_separate'       => false,
            // 读写分离后 主服务器数量
            'master_num'        => 1,
            // 指定从服务器序号
            'slave_no'          => '',
            // 有数据写入后自动读取主服务器
            'read_master'       => false,
            // 是否需要断线重连
            'break_reconnect'   => false,
            // 断线标识字符串
            'break_match_str'   => [],
            // 键名构建器类
            'builder'           => '',
        ],
        // 其它主机连接参数
        'other' => [
            // 主机
            'host' => '192.168.0.2',
            // 端口
            'port' => 6379,
            // 密码
            'password' => 'XXXXXX',
            // 数据库索引
            'select' => 0,
            // 超时时间
            'timeout' => 1
        ],
    ]
]);
~~~

### 简单使用
~~~php
use \axguowen\facade\RedisClient;
// 默认本机
$ping = RedisClient::ping();
// 连接其它服务器
$pingOther = RedisClient::connect('other')->ping();

// set方法
$setKey = RedisClient::set('mykey', 'myvalue');
// 连接其它服务器
$setKeyOther = RedisClient::connect('other')->set('mykey', 'myvalue');
~~~

### 使用键名构造器
~~~php
use \axguowen\facade\RedisClient;

$mykey = RedisClient::key('mykey');

// 设置值
$mykey->set('myvalue');
$value = $mykey->get();
var_dump($value);

// 将当前值改成其它值
$mykey->set('othervalue');
$value = $mykey->get();
var_dump($value);
~~~