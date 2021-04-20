<?php


namespace fysdk\sdk;


class BaseSdk
{
    /**
     * 数据库
     * @var Pdo
     */
    protected $db;

    /**
     * BaseSdk constructor.
     * @param $argument array
     */
    protected function __construct($argument)
    {
       $argument['dns']  = "mysql:host=".$argument['host'].";port=".$argument['port'].";dbname=".$argument['dbName'].";";

       $this->db = Pdo::getInstance([
           $argument['dns'],$argument['username'],$argument['password']
       ]);
    }

    /**
     * 可执行原生sql
     * @param $type  'INSERT'，'UPDATE','DELETE','SELECT'
     * @param $query
     * @throws \Exception
     */
    protected function querySql($type, $query)
    {
        $this->db->query($type, $query);
    }

    /**
     * Sdk Db
     * @return Pdo
     */
    protected function getDb()
    {
        return $this->db;
    }
}