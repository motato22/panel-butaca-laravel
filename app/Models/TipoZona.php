<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoZona extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'tipo_zona';

    /**
     * Los atributos que se pueden asignar de forma masiva.
     *
     * @var array
     */
    protected $fillable = ['tipo'];
    /**
     * Relación: Un tipo de zona puede tener muchas zonas.
     */
    public function zonas()
    {
        return $this->hasMany(Zona::class, 'tipo', 'id');
    }
}
