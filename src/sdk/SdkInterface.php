<?php


namespace fysdk\sdk;


interface SdkInterface
{
    public function getDb();

    public function getTable();

    public function getFd();

    public function setArgument($table, $fd);

}