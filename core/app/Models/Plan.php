<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model {
    use GlobalStatus;

    public function scopeActive($query) {
        $query->where('status', Status::ENABLE);
    }

    public function userPlan() {
        return $this->hasMany(UserPlan::class);
    }

    public function statusPopular(): Attribute {
        return new Attribute(function () {
            $html = '';
            if ($this->is_popular == Status::ENABLE) {
                $html = '<span class="badge badge--info">' . trans('Popular') . '</span>';
            } else {
                $html = '<span class="badge badge--warning">' . trans('General') . '</span>';
            }
            return $html;
        });
    }

    public function saveAmount(): Attribute {
        return Attribute::make(get: fn() => max($this->monthly_price * 12 - $this->yearly_price, 0));
    }
}
