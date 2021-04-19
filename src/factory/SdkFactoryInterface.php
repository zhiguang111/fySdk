<?php

namespace fysdk\factory;

interface SdkFactoryInterface
{
    public static function creatSdkFactory($sdk, $argument);

    public static function getSdkName();

}