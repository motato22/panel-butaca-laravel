<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notificacion extends Model
{
    use SoftDeletes;

    protected $table = 'notificaciones';

    protected $fillable = [
        'titulo',
        'mensaje',
        'fecha',
        'imagen',
        'segmento',
        'dominio',
        'activo',
        'deleted_at'
    ];

    // Para que Eloquent sepa que 'deleted_at' y 'fecha' son campos de tipo fecha
    protected $dates = ['deleted_at', 'fecha'];

    // Si no manejas created_at y updated_at:
    public $timestamps = false;

    // Diccionario para traducir el valor numérico del dominio
    const DOMINIO = [
        'Todos'           => 0,
        'Universitarios'  => 1,
        'Publico_General' => 2,
    ];

    /**
     * Traduce el valor numérico del atributo 'dominio' a su representación textual.
     *
     * @return string
     */
    public function aplicadoA()
    {
        foreach (array_keys(self::DOMINIO) as $key) {
            if (self::DOMINIO[$key] == $this->dominio) {
                return $key;
            }
        }
        return 'Desconocido';
    }
}
