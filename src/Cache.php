<?php
namespace ClevePHP\Extension\mysqlcache;

use Core\Common\Intervene\CoreException;

class Cache
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

    private function _init(\ClevePHP\Extension\mysqlcache\Config $config = null)
    {
        $this->config = $config;
        $this->drive = \ClevePHP\Extension\mysqlcache\Mysqlcache::getInstance($config)->getDrive();
    }

    public function switchDrive(\MysqliDb $drive)
    {
        $this->drive = $drive;
        return $this;
    }

    public function getConfig(): ?\ClevePHP\Extension\mysqlcache\Config
    {
        return $this->config;
    }

    public function getDrive(): ?\MysqliDb
    {
        return $this->drive;
    }

    private function hasTable()
    {
        if ($this->getConfig()->tableName) {
            $result = $this->getDrive()->tableExists($this->getConfig()->tableName);
            if ($result) {
                return true;
            }
        }
        throw new CoreException("Database TableName `table_name` invalid");
    }

    // 写缓存
    public function write($cacheId, $caches, $expTime = 0)
    {
        $this->hasTable();
        if (! is_array($caches)) {
            $caches = [
                $caches
            ];
        }
        if ($this->drive) {
            $data = [
                "id" => $cacheId,
                "caches" => json_encode($caches),
                "create_at" => time(),
                "exptime" => $expTime
            ];
            $storageExists = $this->getDrive()->tableExists($this->getConfig()->tableName . "_storage");
            if ($storageExists) {
                $count = $this->drive->where("id", $cacheId)->getValue($this->getConfig()->tableName . "_storage", "count(*)");
                if ($count) {
                    $this->drive->where("id", $cacheId)->delete($this->getConfig()->tableName);
                    return $this->drive->replace($this->getConfig()->tableName . "_storage", $data);
                }
            }
            return $this->drive->replace($this->getConfig()->tableName, $data);
        }
    }

    // 读缓存
    public function read($cacheId)
    {
        $this->hasTable();
        if ($this->drive) {
            $result = [];
            $storageExists = $this->getDrive()->tableExists($this->getConfig()->tableName . "_storage");
            if ($storageExists) {
                $result = $this->drive->where("id", $cacheId)->getOne($this->getConfig()->tableName . "_storage", "caches");
                if (! $result) {
                    $result = $this->drive->where("id", $cacheId)->getOne($this->getConfig()->tableName, "caches");
                }
            } else {
                $result = $this->drive->where("id", $cacheId)->getOne($this->getConfig()->tableName, "caches");
            }
            if ($result) {
                $result["caches"] = json_decode($result["caches"], true);
                return array_pop($result)[0];
            } else {
                throw new CoreException($this->getDrive()->getLastQuery());
            }
        }
    }

    // 删除缓存
    public function delete($cacheId)
    {
        if ($this->drive) {
            $this->hasTable();
            $storageExists = $this->getDrive()->tableExists($this->getConfig()->tableName . "_storage");
            $result = $this->drive->where("id", $cacheId)->delete($this->getConfig()->tableName);
            if ($storageExists) {
                $result = $this->drive->where("id", $cacheId)->delete($this->getConfig()->tableName . "_storage");
            }
            return $result;
        }
    }
}