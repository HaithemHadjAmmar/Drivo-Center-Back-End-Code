<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prix',
        'durée',
        'description',
        'image'
    ];

    public $timestamps = false;

    public function users()
    {
        return $this->hasMany(User::class);
    }

}
