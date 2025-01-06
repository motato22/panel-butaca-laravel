<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'photo', 'logo', 'video_link', 'address'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Get the photos related to the record
     *
     */
    public function photos()
    {
        return $this->hasMany(ProjectImage::class, 'project_id');
    }

    /**
     * Get the stages related to the record
     *
     */
    public function stages()
    {
        return $this->hasMany(ProjectStage::class, 'project_id')->orderBy('date', 'desc');
    }

    /**
     * Get the blogs related to the record
     *
     */
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'project_id')->orderBy('date', 'desc');
    }

    /**
     * Get the properties related to the record
     *
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'project_id');
    }

    /**
     * Filter rows using specific parameters.
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['user'] ?? null, function($query, $user) {
            if( $user->role_id == 1 ) {// Admin
                //Bring everything because is admin
            } elseif ( $user->role_id == 2 ) {// Apirest // Cliente
                // $query->where('user_id', $user->asociado->id);
            } else {
                #Bring everything because is admin
            }
        })
        ->when($filters['fecha_inicio'] ?? null, function($query, $fecha_inicio) {
            $query->where('created_at', '>=', $fecha_inicio);
        })
        ->when($filters['fecha_fin'] ?? null, function($query, $fecha_fin) {
            $query->where('created_at', '<=', $fecha_fin);
        })
        ->when( $filters['search'] ?? null, function($query, $search) {
            $query->where('name', 'like', '%'.$search.'%');
        })
        ->when($filters['limit'] ?? null, function($query, $limit) {
            $query->limit($limit);
        });
    }
}
