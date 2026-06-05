<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingService extends Model
{
    protected $fillable = ['title','price','status','is_condition','minimum_price','seller_id'];
    public $timestamps = false;

}
