<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function options()
    {
        return $this->hasMany(Option::class, 'product_id');
    }

    public function skus()
    {
        return $this->hasMany(ProductSku::class, 'product_id');
    }

}
