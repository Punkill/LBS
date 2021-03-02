<?php

require_once  __DIR__ . '/../src/vendor/autoload.php';
/* ACCES DB */
$config_ini = parse_ini_file("../src/conf/config.ini");

/* INSTANCE DE CONNEXION  */
$db = new Illuminate\Database\Capsule\Manager();

$db->addConnection($config_ini); /* configuration avec nos paramètres */
$db->setAsGlobal();              /* rendre la connexion visible dans tout le projet */
$db->bootEloquent();             /* établir la connexion */
use \Psr\Http\Message\ServerRequestInterface as Request ;
use \Psr\Http\Message\ResponseInterface as Response ;
use lbs\catalogue\api\controller\Controller;
$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];
/*$configurations = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
    'data' => [
        'sandwichs' => 
        [
            "type" => "collections",
            "count" => 2,
            "sandwichs" =>
            [
                [
                    "ref" => "s2004",
                    "nom" => "le bucheron",
                    "type_pain" => "baguette campagne",
                    "prix" => 6.00,
                    "links" =>
                    [
                        "self" =>
                        [
                            "href" => "/sandwichs/s2004/"
                        ]
                    ]
                ]
            ]
        ]
    ]
*];*/

$c = new \Slim\Container($configuration);
$app = new \Slim\App($c);
/*$app->get('/catalogues[/]',function(Request $req, Response $resp) : Response {
    $sandwichs = $this['data']['sandwichs'];
    $resp = $resp->withHeader('Content-type', 'application/json;charset=utf-8');
    $resp->getBody()->write(json_encode($sandwichs));
    return $resp;
});*/
$app->get('/sandwichs[/]', Controller::class.':showSandwichs')->setName('sandwichs');

$app->get('/sandwichs/{id}[/]', Controller::class.':showSandwich')->setName('showSand');

$app->get('/sandwichs/{id}/categories[/]', Controller::class.':sandwichsCategories')->setName('sandCat');

$app->get('/categories/{id}/sandwichs[/]', Controller::class.':categoriesSandwichs')->setName('catSand');

$app->get('/categories/{id}[/]', Controller::class.':categories')->setName('categories');
$app->run();