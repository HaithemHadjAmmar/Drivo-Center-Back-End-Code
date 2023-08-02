<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nom',
        'prenom',
        'date_naissance',
        'cin',
        'num_tel',
        'email',
        'adresse',
        'prix_heure_code',
        'prix_heure',
        'prix_heure_park',
        'avance',
        'nbr_heure_total_code',
        'nbr_heure_total',
        'nbr_heure_total_park',
        'password',
    ];
    public $timestamps = false;
    
    // relation avec la table Seance
    public function seances()
    {
        return $this->hasMany(Seance::class);
    }

  //relation avec la table examens
    public function examens()
   {
       return $this->hasMany(Examen::class);
   }
   //relation avec la table paiements
   public function paiements()
   {
       return $this->hasMany(Paiment::class);
   }

    //relation avec la table User
    public function user()
    {
        return $this->hasOne(User::class);
    }

}
