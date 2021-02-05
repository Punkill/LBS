<?php
namespace lbs\catalogue\api\model;

class Sandwich extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'sandwich';  /* le nom de la table */
    protected $primaryKey = 'id';     /* le nom de la clé primaire */
    public $timestamps = false;    /* si vrai la table doit contenir les deux colonnes updated_at, created_at */

    public function Categories()
    {
        return $this->belongsToMany('lbs\catalogue\api\model\Categorie', 'sand2cat', 'sand_id', 'cat_id');
    }
}