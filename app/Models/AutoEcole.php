<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Autoecole extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'nom_agence',
        'code_agence',
        'adresse',
        'num_tel',
        'matri_fisc',
        'password'
    ];
    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function pack()
    {
        return $this->hasOne(Pack::class);
    }

    public function candidat()
    {
        return $this->hasMany(Candidat::class);
    }

    public function seance()
    {
        return $this->hasMany(Seance::class);
    }

    public function examen()
    {
        return $this->hasMany(Examen::class);
    }

}
