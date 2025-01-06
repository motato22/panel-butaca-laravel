<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'installments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'property_id', 'installment_status_id', 'amount', 'amount_paid', 'date'
    ];

    /**
     * Get the user related to the record
     *
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the property related to the record
     *
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * Get the status related to the record
     *
     */
    public function status()
    {
        return $this->belongsTo(InstallmentStatus::class, 'installment_status_id');
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


        ->when($filters['property_id'] ?? null, function($query, $property_id) {
            $query->where('property_id', $property_id);
        })
        ->when($filters['owner_id'] ?? null, function($query, $user_id) {
            $query->where('user_id', $user_id);
        })
        ->when($filters['installment_status_id'] ?? null, function($query, $installment_status_id) {
            $query->where('installment_status_id', $installment_status_id);
        })
        ->when($filters['fecha_inicio'] ?? null, function($query, $fecha_inicio) {
            $query->where('date', '>=', $fecha_inicio);
        })
        ->when($filters['fecha_fin'] ?? null, function($query, $fecha_fin) {
            $query->where('date', '<=', $fecha_fin);
        })
        ->when($filters['limit'] ?? null, function($query, $limit) {
            $query->limit($limit);
        });
    }
}
