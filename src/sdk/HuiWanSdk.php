<?php


namespace fysdk\sdk;


use fysdk\sdk\SdkInterface;

class HuiWanSdk extends BaseSdk implements SdkInterface
{
    /**
     * 数据库
     * @var
     */
    private $db;

    private $fd;

    private $table;

    public function __construct($argument)
    {
        $this->db =  parent::__construct($argument);
    }

    /**
     * @return mixed
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * @return mixed
     */
    public function getFd()
    {
        return $this->fd;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    public function setArgument($table, $fd)
    {
        $this->table = $table;
        $this->fd = $fd;
    }

    public function find()
    {

    }

    public function select()
    {

    }
}