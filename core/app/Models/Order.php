<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    protected $casts = [
        'subtotal'      => 'decimal:2',
        'total'         => 'decimal:2',
        'wallet_amount' => 'decimal:8',
        'paid_at'       => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    public function deposit() {
        return $this->hasOne(Deposit::class);
    }

    public function scopePendingPayment($query) {
        return $query->where('status', Status::CATALOG_ORDER_PENDING_PAYMENT);
    }

    public function scopePaid($query) {
        return $query->whereIn('status', [
            Status::CATALOG_ORDER_PAID,
            Status::CATALOG_ORDER_PROCESSING,
            Status::CATALOG_ORDER_COMPLETED,
        ]);
    }

    public function isPaid(): bool {
        return in_array($this->status, [
            Status::CATALOG_ORDER_PAID,
            Status::CATALOG_ORDER_PROCESSING,
            Status::CATALOG_ORDER_COMPLETED,
        ]);
    }

    public function statusBadge(): Attribute {
        return new Attribute(function () {
            $map = [
                Status::CATALOG_ORDER_PENDING_PAYMENT => ['warning', 'Pending Payment'],
                Status::CATALOG_ORDER_PAID            => ['info', 'Paid'],
                Status::CATALOG_ORDER_PROCESSING      => ['primary', 'Processing'],
                Status::CATALOG_ORDER_COMPLETED       => ['success', 'Completed'],
                Status::CATALOG_ORDER_CANCELLED       => ['danger', 'Cancelled'],
            ];

            [$class, $label] = $map[$this->status] ?? ['secondary', ucfirst(str_replace('_', ' ', $this->status))];

            return '<span class="badge badge--' . $class . '">' . trans($label) . '</span>';
        });
    }

    public function paymentStatusBadge(): Attribute {
        return new Attribute(function () {
            if ($this->isPaid()) {
                return '<span class="badge badge--success">' . trans('Paid') . '</span>';
            }

            if ($this->status === Status::CATALOG_ORDER_CANCELLED) {
                return '<span class="badge badge--danger">' . trans('Cancelled') . '</span>';
            }

            return '<span class="badge badge--warning">' . trans('Pending') . '</span>';
        });
    }

    public function paymentSourceBadge(): Attribute
    {
        return new Attribute(function () {
            if ($this->payment_source === Status::PAYMENT_SOURCE_WALLET) {
                return '<span class="badge badge--base">' . trans('Wallet') . '</span>';
            }

            if ($this->payment_source === Status::PAYMENT_SOURCE_GATEWAY) {
                return '<span class="badge badge--primary">' . trans('Gateway') . '</span>';
            }

            if ($this->status === Status::CATALOG_ORDER_PENDING_PAYMENT) {
                return '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            }

            return '<span class="badge badge--dark">' . trans('Legacy') . '</span>';
        });
    }
}
