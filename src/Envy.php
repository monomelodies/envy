<?php

namespace Envy;

class Envy
{
    public function __construct($config = null, callable $callable = null)
    {
        if (isset($config)) {
            $this->loadConfig($config);
        }
        if (isset($callable)) {
            $this->loadEnvironment($callable);
        }
    }

    public static function instance()
    {
        static $instance;
        if (!isset($instance)) {
            $instance = new Envy;
        }
        return $instance;
    }

    public static function setConfig($config)
    {
        self::instance()->loadConfig($config);
    }

    public static function setEnvironment(callable $callable)
    {
        self::instance()->loadEnvironment($callable);
    }
}

