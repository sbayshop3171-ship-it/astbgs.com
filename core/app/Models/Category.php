<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use GlobalStatus;

    protected $casts = [
        'seo_content'=>'object'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeParent($query)
    {
        $query->where('parent_id', 0);
    }

    public function scopeFeatured($query)
    {
        $query->active()->where('featured', Status::YES);
    }

    public function featuredBadge(): Attribute
    {
        return new Attribute(
            get: fn() => $this->featuredBadgeData(),
        );
    }

    public function featuredBadgeData()
    {
        $html = '';
        if ($this->featured == Status::YES) {
            $html = '<span class="badge badge--success">' . trans('Yes') . '</span>';
        } else {
            $html = '<span><span class="badge badge--warning">' . trans('No') . '</span></span>';
        }
        return $html;
    }
}
