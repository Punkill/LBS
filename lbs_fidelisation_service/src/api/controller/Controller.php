<?php
namespace lbs\fidelisation\api\controller;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \lbs\fidelisation\api\models\Carte;
use \Ramsey\Uuid\Uuid;
use \GuzzleHttp\Client;
use \Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;

Class Controller
{
    protected $c;

    public function __construct(\Slim\Container $c){
        $this->c = $c;
    }

    public function auth(Request $req, Response $res,array $args): Response
    {
        $authString = base64_decode(explode(" ",$req->getHeader('Authorization')[0])[1]);
        list($user,$pass) = explode(':',$authString);
        try
        {
            $carte = Carte::select('id','nom_client','mail_client','passwd')->where('id','=',$args['id'])->firstOrFail();

            if(!password_verify($pass, $carte->passwd))
                throw new \Exception("password check failed");

            unset($carte->passwd);
        }
        catch(\Exception $e)
        {
            $res = $res->withStatus(500)
                        ->withHeader('Content-Type','application/json');
            $res->getBody()->write(json_encode($res));
            return $res;
        }
        $token = JWT::encode([
            'iss' => 'http://api.fidelisation.local/auth',
            'aud' => 'http://api.fidelisation.local',
            'iat' => time(),
            'exp' => time()+3600,
            'cid' => $carte->id
        ],
        $this->c->settings['secrets'], 'HS512');

        $data = [
            'carte' => $carte->toArray(),
            'jwt-token' => $token
        ];
        $res = $res->withStatus(200)
                    ->withHeader('Content-Type','application/json');
        $res->getBody()->write(json_encode($data));
        return $res;
    }

    public function getCarte(Request $req, Response $res,array $args): Response
    {
        $token = $req->getAttribute('token');
        $carte = Carte::Select('nom_client','mail_client','cumul_achats','cumul_commandes')->where('id','=',$token->cid)->firstOrFail();
        $res = $res->withStatus(200)
                    ->withHeader('Content-Type','application/json');
        $res->getBody()->write(json_encode($carte));
        return $res;
    }
}