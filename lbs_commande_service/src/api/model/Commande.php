<?php
namespace lbs\command\api\model;
class Commande extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'commande';  /* le nom de la table */
    protected $primaryKey = 'id';     /* le nom de la clé primaire */
    public $timestamps = true;    /* si vrai la table doit contenir les deux colonnes updated_at, created_at */
    public $incrementing = false;
}