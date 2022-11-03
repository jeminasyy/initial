<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'role',
        'password',
        'register_token',
        'email_verified_at',
        'verified',
        'categ_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'register_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'categ_id' => 'array',
    ];

    public function scopeFilter($query, array $filters) {
        if($filters['search'] ?? false) {
            $query->where('id', 'like', '%' . request('search') . '%')
                ->orWhere('email', 'like', '%' . request('search') . '%')
                ->orWhere('firstName', 'like', '%' . request('search') . '%')
                ->orWhere('lastName', 'like', '%' . request('search') . '%')
                ->orWhere('role', 'like', '%' . request('search') . '%');
        }
    }

    // Relationship with Tickets
    public function tickets(){
        return $this->hasMany(Ticket::class, 'user_id');
    }
    
    // Relationship with Categs
    public function categs() {
        return $this->hasManyThrough(Categ::class, 'categ_id');
    }
}
