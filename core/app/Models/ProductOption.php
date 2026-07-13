<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model {
    protected $casts = [
        'price'      => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function files() {
        return $this->hasMany(ProductFile::class);
    }

    public function scopeActive($query) {
        return $query->where('is_active', Status::YES);
    }
}
