<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Evento; 

class Recinto extends Model
{
    use HasFactory;

    protected $table = 'recinto';
    protected $primaryKey = 'id';
    // public $timestamps = false;

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

    /**
     * Relación con el modelo GaleriaRecinto.
     */
    public function galeria()
    {
        return $this->hasMany(GaleriaRecinto::class, 'recinto');
    }

    /**
     * Relación con el modelo User.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'usuario_recinto', 'recinto_id', 'usuario_id');
    }
    public function eventos()
    {
        return $this->hasMany(Evento::class, 'recinto', 'id');
    }
}
