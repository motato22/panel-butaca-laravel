<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 
        'role_id', 'fullname','nombre','correo','username','segmento', 'password', 'change_password', 'photo', 'phone',
        'genre', 'country', 'country_iso', 'date_of_birth', 'remember_token', 'player_id', 
        'apikey', 'clabe', 'payment_token', 'receive_emails', 'receive_notifications', 'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the rol of the model.
     *
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the properties of the model.
     *
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'user_id');
    }
    

    /**
     * Get all the payments related to the record
     *
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    /**
     * Get the all the installments related to the record
     *
     */
    public function installments()
    {
        return $this->hasMany(Installment::class, 'user_id');
    }

    /**
     * Get all of the projects for the user.
     */
    // public function projects()
    // {
    //     return $this->hasManyThrough(
    //         Property::class,
    //         Project::class, 
    //         'property_id', // Foreign key on the deployments table...
    //         'project_id', // Foreign key on the environments table...
    //     );
    // }

    /**
     * Check the role of the current user.
     *
     */
    public function checkRole( $roles )
    {
        foreach ( $roles as $role ) {
            if ( $this->role->name == $role ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Search an user by his email.
     *
     */
    public static function user_by_email($email, $old_email = false)
    {
        $query = User::where('email', '=', $email);

        $query = $old_email ? $query->where('email', '!=', $old_email)->get() : $query->get();
        
        return $query;
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
        ->when($filters['roles'] ?? null, function($query, $roles) {
            $query->when(is_array($roles) ?? null, function($que) use($roles) {
                $que->whereIn('role_id', $roles);
            })
            ->when(! is_array($roles) ?? null, function($que) use ($roles) {
                $que->where('role_id', $roles);
            });
        })
        ->when($filters['only_inactive'] ?? null, function($query) {
            $query->onlyTrashed();
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

        $query->orderBy('id', 'desc');
    }
}
