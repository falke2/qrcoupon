<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $guarded = ['id'];

    public function activations() : HasMany
    {
        return $this->hasMany(Activation::class, 'coupon', 'id');
    }
}
