<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkuValue extends Model
{
    use HasFactory;

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class, 'sku_id');
    }

    public function option()
    {
        return $this->belongsTo(Option::class, 'option_id');
    }

    public function optionValue()
    {
        return $this->belongsTo(OptionValue::class, 'value_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

}
