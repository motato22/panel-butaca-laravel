<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Evento extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $with = ['recinto'];
    protected $table = 'evento';
    protected $fillable = [
        'nombre',
        'recinto',
        'fecha_inicio',
        'fecha_fin',
        'horario',
        'precio_bajo',
        'precio_alto',
        'foto',
        'url_compra',
        'descripcion',
        'facebook',
        'instagram',
        'web',
        'twitter',
        'youtube',
        'snapchat',
        'texto_promocional',
        'video',
        'es_gratuito',
        'recomendado'
    ];

    protected $casts = [
        'horario' => 'array',
        'es_gratuito' => 'boolean',
        'recomendado' => 'boolean'
    ];

    /**
     * Relación con Recinto
     */
    public function recinto()
    {
        return $this->belongsTo(Recinto::class, 'recinto', 'id')->withDefault([
            'nombre' => 'Sin recinto'
        ]);
    }

    /**
     * Relación con Géneros
     */
    public function generos()
    {
        return $this->belongsToMany(Genero::class, 'genero_evento');
    }

    /**
     * Relación con Galería de Eventos
     */
    public function galeria()
    {
        return $this->hasMany(GaleriaEvento::class, 'evento_id');
    }

    /**
     * Relación con Usuarios que dan Like
     */
    public function likeUsuarios()
    {
        return $this->belongsToMany(User::class, 'usuario_like_evento');
    }

    /**
     * Retorna un array de precios (bajo y alto) o 0 si es gratuito
     */
    public function getPreciosAttribute()
    {
        return $this->es_gratuito ? [0] : [$this->precio_bajo, $this->precio_alto];
    }

    /**
     * Devuelve las redes sociales en un array
     */
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

    /**
     * Obtiene las imágenes del evento (incluye la principal y galería)
     */
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

    /**
     * Devuelve el horario en formato legible
     */
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

    /**
     * Convierte un número de día a su abreviatura en español
     */
    public static function intToDia($i)
    {
        $dias = ["Lun.", "Mar.", "Mie.", "Jue.", "Vie.", "Sab.", "Dom."];
        return $dias[$i] ?? "";
    }

    /**
     * Devuelve la URL de la foto principal o una imagen por defecto
     */
    public function getFotoUrlAttribute()
    {
        return $this->foto ? asset('storage/eventos/' . $this->foto) : asset('images/default-evento.png');
    }

    public function getHorarioFormatoAttribute()
    {
        if (!$this->horario) {
            return [];
        }

        $horarios = json_decode($this->horario, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        $diasFormateados = [];

        foreach ($horarios as $fecha => $horas) {
            $diaSemana = Carbon::parse($fecha)->locale('es')->translatedFormat('l'); // Día en español
            $horasFormateadas = implode(', ', array_map(function ($hora) {
                return Carbon::createFromFormat('H:i', $hora)->format('g:i A'); // Formato 12 horas (AM/PM)
            }, $horas));

            $diasFormateados[] = ucfirst($diaSemana) . ': ' . $horasFormateadas;
        }

        return $diasFormateados;
    }
}
