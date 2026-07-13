<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class ProductFile extends Model {
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function option() {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }

    public function scopeActive($query) {
        return $query->where('is_active', Status::YES);
    }
}
