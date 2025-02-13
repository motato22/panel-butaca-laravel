<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GaleriaEvento extends Model
{
    use HasFactory;

    protected $table = 'galeria_evento';

    protected $fillable = [
        'evento_id',
        'image',
    ];

    public $timestamps = false; // Si no vas a usar timestamps en la tabla

    /**
     * Relación con el modelo Evento.
     */
    public function evento()
    {
        return $this->belongsTo(Evento::class, 'evento_id', 'id')->onDelete('cascade');
    }
}
