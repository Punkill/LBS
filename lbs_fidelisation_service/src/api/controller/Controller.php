<?php
namespace lbs\fidelisation\api\controller;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \lbs\command\api\model\Commande;
use \lbs\command\api\model\Item;
use \Ramsey\Uuid\Uuid;
use \GuzzleHttp\Client;
use \Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

Class Controller
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }

    public function auth(Request $req, Response $res,array $args): Response
    {
        $auth = $req->getHeader('Authorization');
        if(!is_null($auth))
        {
            $token = JWT::encode([
                'iss' => 'api.commande.local:',
                'aud' => 'api.fidelisation.local:19280',
                'iat' => time(),
                'exp' => time()+3600,
            ],
            $secret, 'HS512');
        }
        else
        {
            $res = $res->withStatus(401)
                        ->withHeader('Content-Type','application/json');
            $res->getBody()->write(json_encode(array(
                'type' => 'error',
                'error' => 401,
                'message' => 'no authorization header present'
            )));
            return $res;
        }
    }
}