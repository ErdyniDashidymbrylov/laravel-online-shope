<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\database\factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    //    public function hasRole(string $role): bool
    //    {
    //        return $this->roles()->where('slug', $role)->exists();
    //    }
    //    public function hasAnyRole(array $roles): bool
    //    {
    //        return $this->roles()->whereIn('slug', $roles)->exists();
    //    }
    public function hasRole(string $slug): bool
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->contains('slug', $slug);
        }

        return $this->roles()
            ->where('slug', $slug)
            ->exists();
    }

    /**
     * @param array<int, string> $slugs
     */
    public function hasAnyRole(array $slugs): bool
    {
        if ($slugs === []) {
            return false;
        }

        if ($this->relationLoaded('roles')) {
            return $this->roles->whereIn('slug', $slugs)->isNotEmpty();
        }

        return $this->roles()
            ->whereIn('slug', $slugs)
            ->exists();
    }





}
