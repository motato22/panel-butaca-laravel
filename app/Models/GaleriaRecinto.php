<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GaleriaRecinto extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'galeria_recinto';

    protected $fillable = [

        'image',
        'recinto', 
    ];

    public function recinto()
    {
        return $this->belongsTo(Recinto::class, 'recinto');
    }
}

