<?php

namespace fysdk\sdk;

interface SdkFactoryInterface
{
    public static function creatSdkFactory($sdk, $argument);

    public static function getSdkName();

}