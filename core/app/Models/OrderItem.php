<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model {
    protected $casts = [
        'detail'             => 'object',
        'unit_price'         => 'decimal:2',
        'line_total'         => 'decimal:2',
        'last_downloaded_at' => 'datetime',
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function option() {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }
}
