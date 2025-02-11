<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recinto extends Model
{
    use HasFactory;

    protected $table = 'recinto';

    protected $fillable = [
        'zona_id',
        'nombre',
        'foto',
        'contacto',
        'web',
        'horario_inicio',
        'horario_fin',
        'capacidad',
        'facebook',
        'instagram',
        'twitter',
        'youtube',
        'amenidades',
        'descripcion',
        'video',
        'promocion',
        'lat',
        'lng',
        'direccion',
        'telefono'
    ];

    public function zona()
    {
        return $this->belongsTo(Zona::class, 'zona_id');
    }

    public $timestamps = false;
}
