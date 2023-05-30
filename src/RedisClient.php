<?php
// +----------------------------------------------------------------------
// | RedisClient [Simple Redis Client For PHP]
// +----------------------------------------------------------------------
// | Redis客户端
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: axguowen <axguowen@qq.com>
// +----------------------------------------------------------------------

namespace axguowen;

use Psr\Log\LoggerInterface;
use axguowen\redisclient\Connection;

/**
 * Redis客户端类
 */
class RedisClient
{
    /**
     * 连接实例
     * @var array
     */
    protected $instance = [];

    /**
     * 配置
     * @var array
     */
    protected $config = [];

    /**
     * 查询次数
     * @var int
     */
    protected $queryTimes = 0;

    /**
     * 查询日志对象
     * @var LoggerInterface
     */
    protected $log;

    /**
     * 架构方法
     * @access public
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * 初始化配置参数
     * @access public
     * @param array $config 连接配置
     * @return void
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * 获取配置参数
     * @access public
     * @param string $name 配置参数
     * @param mixed $default 默认值
     * @return mixed
     */
    public function getConfig($name = '', $default = null)
    {
        if ('' === $name) {
            return $this->config;
        }

        return isset($this->config[$name]) ? $this->config[$name] : $default;
    }

    /**
     * 设置日志对象
     * @access public
     * @param LoggerInterface $log 日志对象
     * @return void
     */
    public function setLog(LoggerInterface $log)
    {
        $this->log = $log;
    }

    /**
     * 记录连接日志
     * @access public
     * @param string $log 日志信息
     * @param string $type 日志类型
     * @return void
     */
    public function log($log, $type = 'redis')
    {
        if ($this->log) {
            $this->log->log($type, $log);
        }
    }

    /**
     * 创建/切换连接
     * @access public
     * @param string|null $name 连接配置标识
     * @param bool $force 强制重新连接
     * @return Connection
     */
    public function connect($name = null, $force = false)
    {
        return $this->instance($name, $force);
    }

    /**
     * 创建连接实例
     * @access protected
     * @param string|null $name 连接标识
     * @param bool $force 强制重新连接
     * @return Connection
     */
    protected function instance($name = null, $force = false)
    {
        if (empty($name)) {
            $name = $this->getConfig('default', 'localhost');
        }

        if ($force || !isset($this->instance[$name])) {
            $this->instance[$name] = $this->createConnection($name);
        }

        return $this->instance[$name];
    }

    /**
     * 获取连接配置
     * @access protected
     * @param string $name 连接标识
     * @return array
     */
    protected function getConnectionConfig($name)
    {
        $connections = $this->getConfig('connections');
        if (!isset($connections[$name])) {
            throw new \InvalidArgumentException('Undefined redisclient connections config:' . $name);
        }

        return $connections[$name];
    }

    /**
     * 创建连接
     * @access protected
     * @param $name
     * @return Connection
     */
    protected function createConnection($name)
    {
        $config = $this->getConnectionConfig($name);

        /** @var Connection $connection */
        $connection = new Connection($config);
        $connection->setClient($this);

        return $connection;
    }

    /**
     * 更新查询次数
     * @access public
     * @return void
     */
    public function updateQueryTimes()
    {
        $this->queryTimes++;
    }

    /**
     * 重置查询次数
     * @access public
     * @return void
     */
    public function clearQueryTimes()
    {
        $this->queryTimes = 0;
    }

    /**
     * 获得查询次数
     * @access public
     * @return integer
     */
    public function getQueryTimes()
    {
        return $this->queryTimes;
    }
    
    /**
     * 获取所有连接实列
     * @access public
     * @return array
     */
    public function getInstance()
    {
        return $this->instance;
    }

    public function __call($method, array $args)
    {
        return call_user_func_array([$this->connect(), $method], $args);
    }
}
