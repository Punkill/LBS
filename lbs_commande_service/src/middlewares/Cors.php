<?php
namespace lbs\command\api\middlewares;
Class Cors
{
    private $c;

    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }
}