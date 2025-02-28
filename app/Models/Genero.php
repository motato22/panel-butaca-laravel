<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genero extends Model
{
    use HasFactory;

    protected $table = 'generos';
    protected $fillable = ['categoria_id', 'nombre'];

    /**
     * Relación con la tabla Categorias.
     * Un género pertenece a una categoría.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id');
    }

    /**
     * Relación con Evento (muchos a muchos a través de 'genero_evento').
     */
    public function eventos()
    {
        return $this->belongsToMany(Evento::class, 'genero_evento', 'genero_id', 'evento_id');
    }
}
