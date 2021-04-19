<?php

namespace fysdk\factory;

class FactoryModel  implements sdkFactoryInterface
{
    /**
     * @var string
     */
    protected static $sdk;

    /**
     * factoryModel constructor.
     */
    private function __construct()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return mixed
     * Author by(aurora)
     * @param $sdk
     * Data 2021/4/19
     * Time 10:37 上午
     */
    public static function creatSdkFactory($sdk)
    {
        if (is_null(self::$sdk)) {
            self::$sdk = new $sdk();
        }

        return self::$sdk;
    }

    /**
     * @return string
     * Author by(aurora)
     * Data 2021/4/19
     * Time 10:37 上午
     */
    public static function getSdkName()
    {
        return self::$sdk;
    }


    private function __clone()
    {
        // TODO: Implement __clone() method.
    }
}