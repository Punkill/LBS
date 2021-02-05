<?php
namespace lbs\catalogue\api\model;

class Categorie extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'categorie';  /* le nom de la table */
    protected $primaryKey = 'id';     /* le nom de la clé primaire */
    public $timestamps = false;    /* si vrai la table doit contenir les deux colonnes updated_at, created_at */

    public function Sandwichs()
    {
        return $this->belongsToMany('lbs\catalogue\api\model\Sandwich', 'sand2cat', 'cat_id', 'sand_id');
        //return $this->belongsToMany('lbs\catalogue\api\model\Sandwich');
    }
}