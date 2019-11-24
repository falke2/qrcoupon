<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Activation extends Model
{
    protected $guarded = ['id'];

    public function coupon() : BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon', 'id');
    }
}
