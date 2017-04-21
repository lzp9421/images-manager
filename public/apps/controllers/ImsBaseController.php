<?php

use Phalcon\Mvc\Controller;
use Phalcon\Filter;
use Phalcon\Assets\Manager as Assets;
use Phalcon\Mvc\Url;
use Phalcon\Escaper;
use Phalcon\Config\Adapter\Ini as ConfigIni;

abstract class ImsBaseController extends Controller
{
    public function initialize()
    {
        require_once './apps/helpers.php';
        $this->di->set('filter', Filter::class);
        $this->di->set('assets',  Assets::class);
        $this->di->set('url',  Url::class);
        $this->di->set('escaper',  Escaper::class);
        $this->di->set('config', function () {
            $configPath = './apps/config.ini';
            return new ConfigIni($configPath);
        });
    }
}
