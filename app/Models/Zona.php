<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    use HasFactory;

    public $timestamps = false;
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'zona_recinto';

    /**
     * Los atributos que se pueden asignar de forma masiva.
     *
     * @var array
     */
    protected $fillable = ['tipo', 'zona'];

    /**
     * Relación: Una zona puede tener muchos recintos.
     */
    public function recintos()
    {
        return $this->hasMany(Recinto::class, 'zona_id', 'id');
    }

    /**
     * Relación: Una zona pertenece a un tipo de zona.
     */
    public function tipoZona()
    {
        return $this->belongsTo(TipoZona::class, 'tipo', 'id');
    }
}
