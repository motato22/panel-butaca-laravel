<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'evento';
    protected $fillable = [
        'nombre', 'recinto_id', 'fecha_inicio', 'fecha_fin', 'horario',
        'precio_bajo', 'precio_alto', 'foto', 'url_compra', 'descripcion',
        'facebook', 'instagram', 'web', 'twitter', 'youtube', 'snapchat',
        'texto_promocional', 'video', 'es_gratuito', 'recomendado'
    ];

    public function recinto()
    {
        return $this->belongsTo(Recinto::class, 'recinto');
    }

    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'genero_evento');
    }

    public function galeria()
    {
        return $this->hasMany(GaleriaEvento::class, 'evento_id');
    }

    public function likeUsuarios()
    {
        return $this->belongsToMany(User::class, 'usuario_like_evento');
    }

    public function getPreciosAttribute()
    {
        return $this->es_gratuito ? [0] : [$this->precio_bajo, $this->precio_alto];
    }

    public function getRedesAttribute()
    {
        return [
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'web' => $this->web,
            'twitter' => $this->twitter,
            'youtube' => $this->youtube,
            'snapchat' => $this->snapchat,
        ];
    }

    public function getImagenesAttribute()
    {
        $imagenes = $this->foto ? ["uploads/evento/" . $this->foto] : [];

        foreach ($this->galeria as $img) {
            if ($img->image) {
                $imagenes[] = "uploads/evento/" . $img->image;
            }
        }

        return $imagenes;
    }

    public function getHorarioMapAttribute()
    {
        if (!$this->horario) return [];

        $horarios = json_decode($this->horario, true);
        if (!count($horarios)) return [];

        return array_map(function ($h) {
            $dias = $h['dias'];
            $diasTxt = count($dias) > 1 ? "De " . $this->intToDia($dias[0]) . " a " . $this->intToDia(end($dias)) : $this->intToDia($dias[0]);

            return [
                'dias' => $diasTxt,
                'horas' => $h['horas'],
                'dias_numero' => $h['dias']
            ];
        }, $horarios);
    }

    public static function intToDia($i)
    {
        $dias = ["Lun.", "Mar.", "Mie.", "Jue.", "Vie.", "Sab.", "Dom."];
        return $dias[$i] ?? "";
    }
}
