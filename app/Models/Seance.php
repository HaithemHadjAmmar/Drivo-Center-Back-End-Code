<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seance extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidat_id',
        'date',
        'heure_debut',
        'nbr_heure',
        'type',
    ];

    protected $casts = [
        'date' => 'date',
        'heure_debut' => 'datetime:H:i:s',
        'nbr_heure' => 'int',
        'type' => 'string',
    ];

    public $timestamps = false;

    public function candidat()
    {
        return $this->belongsTo(Candidat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
