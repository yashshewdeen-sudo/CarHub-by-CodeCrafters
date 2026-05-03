<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'phone', 'role'];
    protected $hidden   = ['password', 'remember_token'];
    protected $casts    = ['email_verified_at' => 'datetime'];

    /** One-to-many: a user (seller) has many listings. */
    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'seller_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isSeller(): bool
    {
        return $this->role === 'Seller';
    }

    public function isBuyer(): bool
    {
        return $this->role === 'Buyer';
    }
}
