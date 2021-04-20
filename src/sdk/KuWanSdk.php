<?php


namespace fysdk\sdk;


use fysdk\sdk\SdkInterface;

class KuWanSdk extends BaseSdk implements SdkInterface
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