<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $casts = [
        'amount'       => 'decimal:8',
        'post_balance' => 'decimal:8',
        'charge'       => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeWallet($query)
    {
        return $query->where('balance_type', Status::BALANCE_TYPE_WALLET);
    }

    public function scopeEarning($query)
    {
        return $query->where('balance_type', Status::BALANCE_TYPE_EARNING);
    }

    public function balanceTypeBadge(): Attribute
    {
        return new Attribute(function () {
            if ($this->balance_type === Status::BALANCE_TYPE_WALLET) {
                return '<span class="badge badge--base">' . trans('Wallet') . '</span>';
            }

            if ($this->balance_type === Status::BALANCE_TYPE_EARNING) {
                return '<span class="badge badge--primary">' . trans('Earning') . '</span>';
            }

            return '<span class="badge badge--dark">' . trans('Legacy') . '</span>';
        });
    }

    public function balanceTypeLabel(): Attribute
    {
        return new Attribute(function () {
            return match ($this->balance_type) {
                Status::BALANCE_TYPE_WALLET => trans('Wallet'),
                Status::BALANCE_TYPE_EARNING => trans('Earning'),
                default => trans('Legacy'),
            };
        });
    }

}
