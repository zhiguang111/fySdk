<?php

namespace fysdk\factory;

interface SdkFactoryInterface
{
    public static function creatSdkFactory($sdk);

    public static function getSdkName();

}