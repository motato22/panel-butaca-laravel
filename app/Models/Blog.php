<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blogs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id', 'title', 'content'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * Get the project related to the record
     *
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get the photos related to the record
     *
     */
    public function photos()
    {
        return $this->hasMany(BlogImage::class, 'blog_id');
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
        ->when($filters['project_id'] ?? null, function($query, $project_id) {
            $query->where('project_id', $project_id);
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
