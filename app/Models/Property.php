<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'properties';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'project_id', 'user_id', 'card_id', 'name', 'description', 'photo', 'price', 'pay_in_advance', 'monthly_payment', 'signature_date'
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
     * Get the project related to the record
     *
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * Get the customer related to the record
     *
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the card recurrent related to the record
     *
     */
    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id');
    }

    /**
     * Get the valid dates related to the record
     *
     */
    public function photos()
    {
        return $this->hasMany(ProjectImage::class, 'property_id');
    }

    /**
     * Get the payments dates related to the record
     *
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'property_id')->orderBy('payment_date', 'desc');
    }

    /**
     * Get the next payment of the property
     *
     */
    public function nextInstallment()
    {
        return $this->hasMany(Installment::class, 'property_id')->orderBy('date', 'asc')->first();
    }

    /**
     * Get the installments dates related to the record
     *
     */
    public function installments()
    {
        return $this->hasMany(Installment::class, 'property_id')->orderBy('id', 'asc');// Dejar date
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
        ->when($filters['owner_id'] ?? null, function($query, $user_id) {
            $query->where('user_id', $user_id);
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
