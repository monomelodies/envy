<?php

namespace Envy;

use Symfony\Component\Yaml\Yaml;

class Envy
{
    private $configLoaded = false;
    private $current = [];
    private $settings = [];
    private $globals = [];
    private $rebuild = true;
    private static $instance;

    public function __construct($config = null, callable $callable = null)
    {
        if (isset($config)) {
            $this->loadConfig($config);
        }
        if (isset($callable)) {
            $this->loadEnvironment($callable);
        }
        self::$instance = $this;
    }

    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Envy;
        }
        return self::$instance;
    }

    public static function setConfig($config)
    {
        self::instance()->loadConfig($config);
    }

    public static function setEnvironment(callable $callable)
    {
        self::instance()->loadEnvironment($callable);
    }

    private function loadConfig($config)
    {
        $this->rebuild = true;
        if (!file_exists($config)) {
            throw new Config\NotfoundException;
        }
        $ext = substr($config, strrpos($config, '.') + 1);
        switch ($ext) {
            case 'json':
                $settings = json_decode(file_get_contents($config), true);
                if (is_null($settings)) {
                    throw new Config\InvalidException;
                }
                $this->settings += $settings;
                break;
            case 'yml':
            case 'yaml':
                try {
                    $settings = Yaml::parse(file_get_contents($config));
                } catch (\Exception $e) {
                    throw new Config\InvalidException;
                }
                $this->settings += $settings;
                break;
            case 'ini':
                $settings = parse_ini_file($config, true);
                if ($settings === false) {
                    throw new Config\InvalidException;
                }
                $this->settings += $settings;
                break;
            case 'xml':
                $xml = simplexml_load_file($config);
                if ($xml === false) {
                    throw new Config\InvalidException;
                }
                $settings = [];
                foreach ($xml as $env) {
                    $name = $env->getName();
                    $settings[$name] = [];
                    foreach ($env as $prp) {
                        $settings[$name][$prp->getName()] = $prp->__toString();
                    }
                }
                $this->settings += $settings;
                break;
            case 'php':
                $settings = include $config;
                if (!is_array($settings)) {
                    throw new Config\InvalidException;
                }
                $this->settings += $settings;
                break;
            default:
                throw new Config\UnknownFormatException;
        }
        $this->configLoaded = true;
    }

    private function loadEnvironment(callable $callable)
    {
        $this->rebuild = true;
        if (!$this->configLoaded) {
            throw new Config\MissingException;
        }
        $env = $callable($this);
        if (is_string($env)) {
            $env = [$env];
        }
        $this->current = $env;
    }

    public function usingEnvironment($name)
    {
        return in_array($name, $this->current);
    }

    public function __get($name)
    {
        if ($this->rebuild) {
            foreach ($this->current as $env) {
                $this->globals += $this->settings[$env];
            }
            $this->rebuild = false;
        }
        if (isset($this->globals[$name])) {
            return $this->globals[$name];
        }
        if (in_array($name, $this->current)) {
            return true;
        }
        return null;
    }

    public function __set($name, $value)
    {
        $this->globals[$name] = $value;
        $this->rebuild = true;
    }

    public function __isset($name)
    {
        return !is_null($this->__get($name));
    }
}

