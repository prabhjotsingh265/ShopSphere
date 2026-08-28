<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Coupon extends Model
{
    protected $fillable = ["name","discount","valid_until"];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'valid_until' => 'date',
        ];
    }

    /**
     * Convert the coupon name to uppercase
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = Str::upper($value);
    }

    
    /**
     * Check if the coupon is not expired
     */
    public function checkIfValid()
    {
        if($this->valid_until > Carbon::now()) {
            return true;
        }else {
            return false;
        }
    }
}
