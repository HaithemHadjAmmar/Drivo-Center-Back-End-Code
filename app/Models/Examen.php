<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    use HasFactory;
  
    protected $fillable = [
        'candidat_id',
        'date',
        'heure',
        'type',
    ];

    protected $casts = [
        'date' => 'date',
        'heure' => 'datetime:H:i:s',
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
