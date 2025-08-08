<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Relasi ke Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Relasi ke Store
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    // Relasi ke Attendance
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Relasi ke Sale (sebagai kasir)
    public function sales()
    {
        return $this->hasMany(Sale::class, 'cashier_id');
    }

    // Relasi ke Shift
    public function shifts()
    {
        return $this->hasMany(Shift::class, 'cashier_id');
    }

    // Relasi ke ActivityLog
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Relasi ke Notification
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Cek role
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'store_id',
        'phone',
        'skin_preference',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
