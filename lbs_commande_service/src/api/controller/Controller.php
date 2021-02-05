<?php
namespace lbs\command\api\controller;
use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\catalogue\api\model\Sandwich;
use lbs\catalogue\api\model\Categorie;
use Ramsey\Uuid\Uuid;
class Controller
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }
}