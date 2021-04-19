<?php


namespace fysdk\sdk;


class BaseSdk
{
    /**
     * BaseSdk constructor.
     * @param $argument array
     */
    public function __construct($argument)
    {
        return Pdo::getInstance([
            $argument['dns'],$argument['username'],$argument['password']
        ]);
    }
}