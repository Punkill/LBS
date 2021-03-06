<?php
require_once  __DIR__ . '/../src/vendor/autoload.php';
$config_ini = parse_ini_file("../src/conf/config.ini");

$config_slim = require_once('conf/Settings.php'); /* Récupération de la config de Slim */
$errors = require_once('conf/Errors.php'); /* Récupération des erreurs */
$db = new Illuminate\Database\Capsule\Manager();

$db->addConnection($config_ini); /* configuration avec nos paramètres */
$db->setAsGlobal();              /* rendre la connexion visible dans tout le projet */
$db->bootEloquent();             /* établir la connexion */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\command\api\controller\Controller;


$c = new \Slim\Container(array_merge($config_slim, $errors));
$app = new \Slim\App($c);
$app->get('/commandes[/]',Controller::class.':getCommandes')->setName('getCommandes');
$app->post('/commandes[/]', Controller::class.':createCommande')->setName('createCommande');
$app->get('/commandes/{id}[/]', Controller::class.':getCommande')->setName('getCommande');
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});
$app->run();