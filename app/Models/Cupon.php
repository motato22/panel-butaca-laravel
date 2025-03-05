<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    protected $table = 'cupon';

    protected $fillable = [
        'dominio',
        'descripcion',
        'activo',
        'eliminado',
        'fecha_inicio',
        'notificacoin', // así está en la bd original 
    ];

    public $timestamps = false;

    // Diccionario para traducir el valor numérico del dominio
    const DOMINIO = [
        'TODOS'           => 0,
        'UNIVERSITARIOS'  => 1,
        'PUBLICO_GENERAL' => 2,
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
