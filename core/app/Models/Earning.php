<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }


    public function earning(): Attribute
    {
        return new Attribute(
            get: fn() => $this->category_commission + $this->level_commission,
        );
    }
}
