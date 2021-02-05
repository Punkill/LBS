<?php
require_once  __DIR__ . '/../src/vendor/autoload.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\command\api\controller\Controller;
$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
$c = new \Slim\Container($configuration);
$app = new \Slim\App();
$app->get('/hello',function(Request $req, Response $res, array $args) : Response
{
    $res = $res->withStatus(200)
                ->withHeader('Content-Type','application/json');
    $res->getBody()->write(json_encode("<h1>Test</h1>"));
return $res;
});
$app->post('/commandes[/]', Controller::class.':showSandwichs')->setName('sandwichs');

$app->run();