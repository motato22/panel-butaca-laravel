<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    
    protected $table = 'banners';

    
    protected $fillable = [
        'descripcion',
        'imagen',
        'texto',
        'ubicacion',
        'ubicacion_imagen',
        'activo',
        'url',
        'fecha_inicio',
        'fecha_fin',
        'fecha_creacion',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_inicio'   => 'date',
        'fecha_fin'      => 'date',
    ];

    public $timestamps = false;
    
    // protected $dates = [
    //     'fecha_inicio',
    //     'fecha_fin',
    //     'fecha_creacion',
    // ];

    
}
