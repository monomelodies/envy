<?php

namespace Envy\Tests;

use PHPUnit_Framework_TestCase;
use Envy\Envy;

class EnvyTest extends PHPUnit_Framework_TestCase
{
    private function config($env)
    {
        return function () use ($env) {
            return $env;
        };
    }

    private function runtests($config)
    {
        $config = dirname(__FILE__)."/$config";
        foreach (['test' => 'bar', 'prod' => 'baz'] as $env => $check) {
            $envy = new Envy($config, $this->config($env));
            $this->assertEquals($check, $envy->foo);
        }
    }

    public function testJson()
    {
        $this->runtests('json.json');
    }

    public function testIni()
    {
        $this->runtests('ini.ini');
    }

    public function testPhp()
    {
        $this->runtests('php.php');
    }

    public function testYaml()
    {
        $this->runtests('yaml.yml');
    }

    public function testXml()
    {
        $this->runtests('xml.xml');
    }
}

