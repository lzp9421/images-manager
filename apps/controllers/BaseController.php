<?php

use Phalcon\Mvc\Controller;
use Phalcon\Filter;

abstract class BaseController extends Controller
{
    public function initialize()
    {
        require_once dirname(__FILE__) . '/../../vendor/autoload.php';
        $this->di->set('filter', Filter::class);
    }
}
