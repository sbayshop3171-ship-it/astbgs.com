<?php


namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class UserPlan extends Model
{
    public function scopePaid($query)
    {
        $query->where('is_payment', Status::PAID_SUBSCRIPTION);
    }

    public function scopeUnpaid($query)
    {
        $query->where('is_payment', Status::UNPAID_SUBSCRIPTION);
    }

    public function scopeActive($query)
    {
        $query->where('status', Status::PLAN_ACTIVE);
    }

    public function scopePending($query)
    {
        $query->where('status', Status::PLAN_PENDING);
    }

    public function scopeExpired($query)
    {
        $query->where('status', Status::PLAN_EXPIRED);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function(){
            $html = '';
            if($this->status == Status::PLAN_PENDING){
                $html = '<span class="badge badge--warning">'.trans('Pending').'</span>';
            }elseif($this->status == Status::PLAN_ACTIVE){
                $html = '<span><span class="badge badge--success">'.trans('Active').'</span>';
            }elseif($this->status == Status::PLAN_EXPIRED){
                $html = '<span><span class="badge badge--danger">'.trans('Expired').'</span><br><small>'.diffForHumans($this->updated_at).'</small></span>';
            }
            return $html;
        });
    }

    public function paymentBadge(): Attribute
    {
        return new Attribute(function(){
            $html = '';
            if($this->is_payment == Status::UNPAID_SUBSCRIPTION){
                $html = '<span class="badge badge--warning">'.trans('Unpaid').'</span>';
            }elseif($this->is_payment == Status::PAID_SUBSCRIPTION){
                $html = '<span><span class="badge badge--success">'.trans('Paid').'</span>';
            }
            return $html;
        });
    }
}
