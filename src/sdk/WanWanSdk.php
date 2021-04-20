<?php


namespace fysdk\sdk;

class WanWanSdk extends BaseSdk
{
    /**
     * 数据库
     * @var Pdo
     */
    private $db;

    public function __construct($argument)
    {
        $this->db =  parent::__construct($argument);
    }

    /**
     * 可执行原生sql
     * @param $type  'INSERT'，'UPDATE','DELETE','SELECT'
     * @param $query
     * @throws \Exception
     */
    public function querySql($type, $query)
    {
        $this->db->query($type, $query);
    }

}