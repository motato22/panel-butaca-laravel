<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categorias';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['nombre', 'thumbnail', 'background'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * Get the related generos.
     */
    public function generos()
    {
        return $this->hasMany(Genero::class, 'categoria_id', 'id');
    }

    /**
     * Accessor for thumbnail URL.
     */
    public function getThumbnailUrlAttribute()
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : null;
    }

    /**
     * Mutator for setting background color.
     */
    public function setBackgroundAttribute($value)
    {
        $this->attributes['background'] = strtolower($value);
    }
}
