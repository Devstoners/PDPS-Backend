<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable , HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',//0 = Unregistered, 1 = Active, 2= Disabled
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
        'password' => 'hashed',
    ];

    public function member()
    {
        return $this->hasOne(Member::class, 'user_id');
    }

    public function officer()
    {
        return $this->hasOne(Officer::class, 'user_id');
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class, 'user_id');
    }

    public function waterCustomer()
    {
        return $this->hasOne(WaterCustomer::class, 'user_id');
    }

    public function taxCustomer()
    {
        return $this->hasOne(TaxCustomer::class, 'user_id');
    }

    public function hallReserveCustomer()
    {
        return $this->hasOne(HallCustomer::class, 'user_id');
    }

}
