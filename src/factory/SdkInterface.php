<?php


namespace fysdk\factory;


interface SdkInterface
{
    public function getDb();

    public function getTable();

    public function getFd();

    public function setArgument($table, $fd);

}