<?php
namespace lbs\catalogue\api\controller;
use\Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use lbs\catalogue\api\model\Sandwich;
use lbs\catalogue\api\model\Categorie;
class Controller
{
    protected $c;

    public function __construct(\Slim\Container $c = null)
    {
        $this->c = $c;
    }
    public function showSandwichs(Request $req, Response $res) : Response
    {
        $type = $req->getQueryParam('t',null);
        $page = $req->getQueryParam('page',1);
        $size = $req->getQueryParam('size',2);
        $sandwichs = Sandwich::select()->orderBy('nom');
        if(!is_null($type))
        {
            $sandwichs = $sandwichs->where('type_pain','like',"%$type%");
        }
        $prev = 0;
        $count = $sandwichs->count();
        $result['type'] = "collection";
        $result['count'] = $count;
        $result['size'] = $size;
        if($page<=0)
            $page = 1;
        $last = intdiv($result['count'],$size)+1;
        if($page > $last) $page = $last;
        $rows = $sandwichs->skip(($page-1)*$size)->take($size)->get();
        if(($page - 1)==0)
            $prev = 1;
        else
            $prev = $page-1;
        $url_sandwichs = $this->c->get('router')->pathFor('sandwichs',[]);
        $result['links'] = array(
            'next' => array(
                'href' => $url_sandwichs."?page=".($page + 1)."&size=$size"
            ),
            'prev' => array(
                'href' => $url_sandwichs."?page=".$prev ."&size=$size"
            ),
            'last' => array(
                'href' => $url_sandwichs."?page=$last&size=$size"
            ),
            'first' => array(
                'href' => $url_sandwichs."?page=1&size=$size"
            )
        );
        for($i=0;$i<$rows->count();$i++)
        {
            $result['sandwichs'][$i] = array(
                'sandwich' => $rows[$i],
                'links' => array(
                    'self' => array(
                        'href' => $this->c->get('router')->pathFor('showSand',['id'=>$rows[$i]['id']])
                    )
                )
            );
        };
        //$result['sandwichs'] = $rows;
        $res = $res->withStatus(200)
                    ->withHeader('Content-Type','application/json');
        $res->getBody()->write(json_encode($result));
        return $res;
    }

    public function showSandwich(Request $req, Response $res,array $args): Response
    {
        $id = $args['id'];
        /*$sandwich = Sandwich::where('id','=',$id)->whereHas('Categories',function($q){
            $q->select('id','nom');
        })->first();*/
        $sandwich = Sandwich::select()->where('id','=',$id)->with('Categories')->first();
        if($sandwich != null)
        {
            $url_Sand = $this->c->get('router')->pathFor('showSand',['id'=>$id]);
            $url_SandCat = $this->c->get('router')->pathFor('sandCat',['id'=>$id]);
            $result['type'] = "resource";
            $result['links'] = array(
                'links' => array(
                    'self' => array(
                        'href' => $url_Sand
                    ),
                    'categories' => array(
                        'href' => $url_SandCat
                    )
                )
            );
            $result['sandwich'] = $sandwich;
            //Demander a Mr Canals, pourquoi j'ai la table pivot avec les categories
            foreach($result['sandwich']['categories'] as $categorie)
            {
                unset($categorie['pivot']);
            }
    
            $res = $res->withStatus(200)
                        ->withHeader('Content-Type','application/json');
            $res->getBody()->write(json_encode($result));
            return $res;
        }
        else
        {
            $res = $res->withStatus(404)
                        ->withHeader('Content-Type','application/json');
            $res->getBody()->write(json_encode("Sandwich Not Found"));
            return $res;
        }
    }

    public function sandwichsCategories(Request $req, Response $res,array $args): Response
    {

    }
    
    public function categoriesSandwichs(Request $req, Response $res,array $args): Response
    {
        $id = $args['id'];
        $categorie = Categorie::Select("id","nom","description")->where('id','=',$id)->first();
        $result['type'] = "resource";
        $result['date'] = date("d-m-Y");
        $result['categorie'] = $categorie;
        //$result['sandwichs'] = $categorie->Sandwichs()->get();
        //unset($result['sandwichs'][0]['pivot']);
        $sandwichs = $categorie->Sandwichs()->get();
        foreach($sandwichs as $sandwich)
        {
            unset($sandwich['pivot']);
        }
        $result['sandwichs'] = $sandwichs;
        $res = $res->withStatus(200)
                    ->withHeader('Content-Type','application/json');
        $res->getBody()->write(json_encode($result));
        return $res;
    }

    public function categories(Request $req, Response $res,array $args): Response
    {
        $id = $args['id'];
        $categorie = Categorie::Select("id","nom","description")->where('id','=',$id)->first();
        $result['type'] = "resource";
        $result['date'] = date("d-m-Y");
        $result['categorie'] = $categorie;
        $url_CatSand = $this->c->get('router')->pathFor('catSand',['id'=>$id]);
        $url_Cat = $this->c->get('router')->pathFor('categories',['id'=>$id]);
        $result['links'] = array(
            'sandwichs' => array(
                'href' => $url_CatSand
            ),
            'self' => array(
                'href' => $url_Cat
            )
        );
        $res = $res->withStatus(200)
                    ->withHeader('Content-Type','application/json');
        $res->getBody()->write(json_encode($result));
        return $res;
    }
}