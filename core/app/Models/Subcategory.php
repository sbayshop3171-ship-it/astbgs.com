<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    use GlobalStatus;

     protected $casts = [
        'seo_content'=>'object'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function reviewers()
    {
        return $this->belongsToMany(Reviewer::class);
    }
}
