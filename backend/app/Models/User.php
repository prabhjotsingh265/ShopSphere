<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'address', 'city', 'zip_code', 'country', 'phone_number', 'profile_image', 'profile_completed'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

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

    protected $appends = [
        'image_path'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class)
            ->with('products')
            ->latest();
    }

    public function getImagePathAttribute()
    {
        if($this->profile_image)
        {
            return asset($this->profile_image);
        }else {
            return "https://cdn.pixabay.com/photo/2017/11/10/05/48/user-2935527_1280.png";
        }
    }
}
