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
        'nombre',
        'username',
        'correo',
        'password',
        'provider_id',
        'provider_uid',
        'role',
        'activo',
        'no_push',
        'fecha_nacimiento',
        'genero',
        'recovery_date',
        'telefono',
        'cuenta_verificada',
        'foto',
        'foto_url',
        'fcm_token',
        'codigo_ude_g',
        'nip',
        'segmento',
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

    protected $casts = [
        'email_verified_at' => 'datetime',
        'activo' => 'boolean',
        'no_push' => 'boolean',
        'cuenta_verificada' => 'boolean',
        'recovery_date' => 'datetime',
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
    public function checkRole($roles)
    {
        foreach ($roles as $role) {
            if ($this->role->name == $role) {
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
        $query = User::where('correo', '=', $email);

        $query = $old_email ? $query->where('correo', '!=', $old_email)->get() : $query->get();

        return $query;
    }

    /**
     * Filter rows using specific parameters.
     */
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['user'] ?? null, function ($query, $user) {
            $query->when($user->role_id == 1 ?? null, function ($query) use ($user) { // Admin
                // All data
            })
                ->when($user->role_id == 2 ?? null, function ($query) use ($user) { // Customer
                    // Customer data
                });
        })
            ->when($filters['roles'] ?? null, function ($query, $roles) {
                $query->when(is_array($roles) ?? null, function ($que) use ($roles) {
                    $que->whereIn('role_id', $roles);
                })
                    ->when(! is_array($roles) ?? null, function ($que) use ($roles) {
                        $que->where('role_id', $roles);
                    });
            })
            ->when($filters['only_inactive'] ?? null, function ($query) {
                $query->onlyTrashed();
                // $query->withTrashed();
            })
            ->when($filters['nombre'] ?? null, function ($query, $nombre) {
                $query->where('fullname', 'like', '%' . $nombre . '%');
            })
            ->when($filters['fecha_inicio'] ?? null, function ($query, $fecha_inicio) {
                $query->where('created_at', '>=', $fecha_inicio);
            })
            ->when($filters['fecha_fin'] ?? null, function ($query, $fecha_fin) {
                $query->where('created_at', '>=', $fecha_fin);
            });

        $query->orderBy('id', 'desc');
    }

    public function recintos()
    {
        return $this->belongsToMany(Recinto::class, 'usuario_recinto', 'user_id', 'recinto_id');
    }

    /**
     * Verifica si el usuario tiene un rol específico.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }
}
