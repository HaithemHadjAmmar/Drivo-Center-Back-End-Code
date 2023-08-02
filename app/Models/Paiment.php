<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiment extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidat_id',
        'montant',
        'date_paiement',
    ];

    protected $casts = [
        'montant' => 'double',
        'date_paiement' => 'date',
    ];

    public $timestamps = false;

    public function users()
    {
        return $this->hasMany(User::class);
    }
}


