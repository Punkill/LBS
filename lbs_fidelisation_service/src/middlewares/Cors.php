<?php
namespace lbs\fidelisation\middlewares;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
Class Cors
{
    private $c;

    public function __construct(\Slim\Container $c)
    {
        $this->c = $c;
    }

    public function verificationAjoutHeader(Request $req, Response $res, callable $next): Response
    {
        if(!$req->hasHeader('Origin'))
        {
            $res = $res->withStatus(401)
                        ->withHeader('Content-Type','application/json');
            $res->getBody()->write(json_encode("Missing Origin Header"));
            return $res;
        }

        $response = $next($req,$res);

        $response = $response->withHeader('Access-Control-Allow-Origin', $req->getHeader('Origin'))
                    ->withHeader('Access-Control-Allow-Methods',$this->c['settings']['cors']['methods'])
                    ->withHeader('Access-Control-Allow-Headers',$this->c['settings']['cors']['headers'])
                    ->withHeader('Access-Control-Allow-Max-Age',$this->c['settings']['cors']['maxAge'])
                    ->withHeader('Access-Control-Allow-Credentials',true);
        return $response;
    }
}