<?php
namespace ClevePHP\Extension\mysqlcache;

class Config
{

    private static $instance;

    private function __construct()
    {}

    private function __clone()
    {}

    static public function getInstance()
    {
        if (! self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public $host = "localhost";

    public $port = "3306";

    public $user = "";

    public $password = "";

    public $dbname;

    public $tableName = "caches";

    public $timeout = 5;

    public $charset = "utf8mb4";

    public $connection_num = 2;

    public $prefix;
    
    public $tag="mysql_caches";

    public function loadConfig($config = [])
    {
        if ($config) {
            $this->host = isset($config["host"]) ? $config["host"] : $this->host;
            $this->user = isset($config["user"]) ? $config["user"] : $this->user;
            $this->port = isset($config["port"]) ? $config["port"] : $this->port;
            $this->password = isset($config["password"]) ? $config["password"] : null;
            $this->dbname = isset($config["dbname"]) ? $config["dbname"] : $this->dbname;
            $this->tableName = isset($config["table_name"]) ? $config["table_name"] : $this->tableName;
            $this->timeout = isset($config["timeout"]) ? $config["timeout"] : $this->timeout;
            $this->charset = isset($config["charset"]) ? $config["charset"] : $this->charset;
            $this->tag = isset($config["tag"]) ? $config["tag"] : $this->tag;
            $this->connection_num = isset($config["connection_num"]) ? $config["connection_num"] : $this->connection_num;
            $this->prefix = isset($config["prefix"]) ? $config["prefix"] : $this->prefix;
        }
        return $this;
    }
}