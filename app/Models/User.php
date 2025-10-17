<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Product;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Review;
use App\Models\Wishlist;

/**
 * @property Cart|null $cart
 * @property \Illuminate\Database\Eloquent\Collection|Product[] $products
 * @property \Illuminate\Database\Eloquent\Collection|Order[] $orders
 * @property \Illuminate\Database\Eloquent\Collection|Review[] $reviews
 * @property \Illuminate\Database\Eloquent\Collection|Wishlist[] $wishlists
 *
 * @method HasOne cart()
 * @method HasMany products()
 * @method HasMany orders()
 * @method HasMany reviews()
 * @method HasMany wishlists()
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }
}
