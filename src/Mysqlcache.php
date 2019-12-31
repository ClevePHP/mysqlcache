<?php
namespace ClevePHP\Extension\mysqlcache;

use Core\Util\Db;

class Mysqlcache
{

    private static $instance;

    private function __construct($config = null)
    {
        $this->_init($config);
    }

    private function __clone()
    {}

    private $request = null;

    private $driveName;

    private $drive;

    private $config;

    static public function getInstance(\ClevePHP\Extension\mysqlcache\Config $config = null)
    {
        if (! self::$instance instanceof self) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function getConfig(): ?\ClevePHP\Extension\mysqlcache\Config
    {
        return $this->config;
    }

    private function _init(\ClevePHP\Extension\mysqlcache\Config $config = null)
    {
        $this->config=$config;
        $config = (array) $config;
        $client = Db::mysqli(null, $config);
        $this->drive = $client;
    }

    public function switchConfig(\ClevePHP\Extension\mysqlcache\Config $config)
    {
        $this->_init($config);
        return $this;
    }

    public function getDrive():?\MysqliDb
    {
        return $this->drive;
    }
}