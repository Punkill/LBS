<?php
namespace lbs\command\api\controller;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \lbs\command\api\model\Commande;
use \lbs\command\api\model\Item;
use \Ramsey\Uuid\Uuid;
use \GuzzleHttp\Client;
class Controller
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }

    public function createCommande(Request $req, Response $res,array $args): Response
    {
        $client = new Client([
            'base_uri' => 'http://api.catalogue.local',
            'auth' => ['cat_lbs','cat_lbs','digest']
            //'timeout' => 3.0,
        ]);
        $bodyReq = json_decode($req->getBody());
        $items = $bodyReq->items;
        $commande = new Commande();
        $uuid1 = Uuid::uuid1();
        $token = random_bytes(32);
        $token = bin2hex($token);
        $date = date_create_from_format('d-m-Y',$bodyReq->livraison->date);
        $heure = date_create_from_format('H:i', $bodyReq->livraison->heure);
        $montant = 0;
        $commande->nom = filter_var($bodyReq->nom,FILTER_SANITIZE_SPECIAL_CHARS);
        $commande->mail = filter_var($bodyReq->mail,FILTER_VALIDATE_EMAIL);
        $commande->id = $uuid1;
        $commande->token = $token;
        $commande->livraison = $date->format('Y-m-d').' '.$heure->format('H:i:s');
        $commande->status = 1;
        $resultat = array();
        foreach($items as $item)
        {
            try
            {
                $response = $client->get($item->uri);
                $body = json_decode($response->getBody());
                $commande->montant += $item->q * $body->sandwich->prix;
                $itemDB = new Item();
                $itemDB->uri = $item->uri;
                $itemDB->libelle = $body->sandwich->nom;
                $itemDB->tarif = $body->sandwich->prix;
                $itemDB->quantite = $item->q;
                $itemDB->command_id = $commande->id;
                $itemDB->save();
                array_push($resultat,array(
                    'uri' => $itemDB->uri,
                    'libelle' => $itemDB->libelle,
                    'tarif' => $itemDB->tarif,
                    'quantite' => $itemDB->quantite
                ));
            }
            catch(\Exception $e)
            {
                //Erreur not found a compléter
                $code = $response->getStatusCode();
                $commande->delete();
            }
        }
        //Save de la commande
        try
        {
            $commande->save();
        }
        catch(\Exception $e)
        {
            $res = $res->withStatus(500)
                        ->withHeader('Content-Type','application/json');
            $res->getBody()->write(json_encode($e->getmessage()));
            return $res;
        }
        $res = $res->withStatus(201)
                    ->withHeader('Content-Type','application/json');
        $res->getBody()->write(json_encode(array(
            'type' => 'resource',
            'links' => array(
                'self' => $this->c->get('router')->pathFor('getCommande',['id' => $commande->id]),
                'items' => $this->c->get('router')->pathFor('getCommande',['id'=> $commande->id]).'items'
            ),
            'commande' => array(
                'id' => $commande->id,
                'nom' => $commande->nom,
                'mail' => $commande->mail,
                'status' => $commande->status,
                'livraison' => $commande->livraison,
                'token' => $commande->token,
                'montant' => $commande->montant,
                'items' => $resultat
            )
        )));
        return $res;
    }

    public function getCommande(Request $req, Response $res,array $args) : Response
    {
        $token = $req->getQueryParam('token',null);
        $header = $req->getHeader('X-lbs-token');
        try
        {
            $commande = Commande::where('id','=',$args['id'])->firstorFail();
        }
        catch(Exception $e)
        {
            $res = $res->withStatus(404)
                        ->withHeader('Content-Type','application/json');
            $res->getBody()->write(json_encode(array('error' => 'Command not found')));
            return $res;
        }
        //passer en middleware
        if($commande->token == ($token || $header))
        {
            $res = $res->withStatus(200)
                        ->withHeader('Content-Type','application/json');
            $res->getBody()->write(json_encode($commande));
            return $res;
        }
        else
        {
            $res = $res->withStatus(401)
                        ->withHeader('Content-Type','application/json');
            $res->getBody()->write(json_encode(array('error' => 'Unauthorized')));
            return $res;
        }
    }
}