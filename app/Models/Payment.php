<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'property_id', 'payment_type_id', 'payment_status_id', 'amount', 'payment_date', 'photo', 'clabe', 'oxxopay_reference'
    ];

    /**
     * Get payments
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    // protected function paymentDate(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => ucfirst($value),
    //     );
    // }

    /**
     * Get the user related to the record
     *
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the property related to the record
     *
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id')->withTrashed();
    }

    /**
     * Get the type related to the record
     *
     */
    public function type()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }

    /**
     * Get the status related to the record
     *
     */
    public function status()
    {
        return $this->belongsTo(PaymentStatus::class, 'payment_status_id');
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
            $query->whereHas('property', function($que) use ($project_id) {
                $que->where('project_id', $project_id);
            });
        })
        ->when($filters['property_id'] ?? null, function($query, $property_id) {
            $query->where('property_id', $property_id);
        })
        ->when($filters['owner_id'] ?? null, function($query, $user_id) {
            $query->where('user_id', $user_id);
        })
        ->when($filters['payment_status_id'] ?? null, function($query, $payment_status_id) {
            $query->where('payment_status_id', $payment_status_id);
        })
        ->when($filters['payment_type_id'] ?? null, function($query, $payment_type_id) {
            $query->where('payment_type_id', $payment_type_id);
        })
        ->when($filters['fecha_inicio'] ?? null, function($query, $fecha_inicio) {
            $query->where('payment_date', '>=', $fecha_inicio);
        })
        ->when($filters['fecha_fin'] ?? null, function($query, $fecha_fin) {
            $query->where('payment_date', '<=', $fecha_fin);
        })
        ->when($filters['limit'] ?? null, function($query, $limit) {
            $query->limit($limit);
        });
    }
}
