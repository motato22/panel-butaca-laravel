<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewData extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'news';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['new_category_id', 'photo', 'link'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the category related to the record
     *
     */
    public function category()
    {
        return $this->belongsTo(NewCategory::class, 'new_category_id');
    }

    /**
     * Filter rows using specific parameters.
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['user'] ?? null, function($query, $user) {
            $query->when( $user->role_id == 1 ?? null, function($query) use($user) {// Admin
                // All data
            })
            ->when( $user->role_id == 2 ?? null, function($query) use($user) {// Customer
                // Customer data
            });
        })
        ->when($filters['status'] ?? null, function($query, $status) {
            // $status == 1 ?
            // $query->onlyTrashed();
            // $query->withTrashed();
        })
        ->when($filters['nombre'] ?? null, function($query, $nombre) {
            $query->where('fullname', 'like', '%'.$nombre.'%');
        })
        ->when($filters['fecha_inicio'] ?? null, function($query, $fecha_inicio) {
            $query->where('created_at', '>=', $fecha_inicio);
        })
        ->when($filters['fecha_fin'] ?? null, function($query, $fecha_fin) {
            $query->where('created_at', '>=', $fecha_fin);
        });
    }
}
