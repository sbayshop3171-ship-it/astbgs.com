<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Product extends Model {
    protected $casts = [
        'attribute_info'   => 'object',
        'tags'             => 'object',
        'managed_by_admin' => 'boolean',
        'is_published'     => 'boolean',
        'base_price'       => 'decimal:2',
    ];

    public function getMyProductAttribute() {
        return auth()->id() == $this->getAttribute('user_id');
    }

    public function scopeFeatured($query) {
        return $query->where('is_featured', Status::ENABLE);
    }

    public function isTrending() {

        $topViewedProducts = ProductView::selectRaw('product_id, SUM(views) as total_views')
            ->whereBetween('views_date', [now()->subDays(7), now()])
            ->groupBy('product_id')
            ->orderBy('total_views', 'desc')
            ->limit(gs('trending_count'))
            ->pluck('product_id')
            ->toArray();

        return in_array($this->id, $topViewedProducts);
    }

    public function author() {
        return $this->belongsTo(User::class, 'user_id')->withDefault(function ($user) {
            $user->firstname = gs('site_name');
            $user->lastname  = '';
            $user->username  = 'catalog';
        });
    }

    public function scopePending($query) {
        return $query->where('status', Status::PRODUCT_PENDING);
    }

    public function scopeCountComment($query) {
        return $query->withCount([
            'comments' => function ($query) {
                $query->where('review_id', 0)->where('parent_id', 0);
            },
        ]);
    }

    public function scopeAllActive($query) {
        $query->whereHas('category', function ($q) {
            $q->active();
        })->whereHas('subcategory', function ($q) {
            $q->active();
        })->where(function ($q) {
            $q->where('managed_by_admin', Status::YES)->orWhereHas('author', function ($authorQuery) {
                $authorQuery->active();
            });
        });
    }

    public function scopeCatalogManaged($query) {
        return $query->where('managed_by_admin', Status::YES);
    }

    public function scopeCatalogPublished($query) {
        return $query->catalogManaged()
            ->where('is_published', Status::YES)
            ->where('availability_status', '!=', Status::PRODUCT_AVAILABILITY_UNAVAILABLE)
            ->approved()
            ->allActive();
    }

    public function scopeApproved($query) {
        return $query->where('status', Status::PRODUCT_APPROVED);
    }

    public function scopeHardRejected($query) {
        return $query->where('status', Status::PRODUCT_HARD_REJECTED);
    }

    public function scopeSoftRejected($query) {
        return $query->where('status', Status::PRODUCT_SOFT_REJECTED);
    }

    public function scopeDown($query) {
        return $query->where('status', Status::PRODUCT_DOWN);
    }

    public function scopeFileUpdated($query) {
        return $query->where('product_updated', 1);
    }

    public function scopeUpdatePending($query) {
        return $query('product_updated', Status::PRODUCT_UPDATE_PENDING);
    }

    public function scopeUpdateApproved($query) {
        return $query('product_updated', Status::PRODUCT_UPDATE_APPROVED);
    }

    public function scopeUpdateSoftRejected($query) {
        return $query('product_updated', Status::PRODUCT_UPDATE_SOFT_REJECT);
    }

    public function scopeUpdateHardRejected($query) {
        return $query('product_updated', Status::PRODUCT_UPDATE_HARD_REJECT);
    }

    public function scopePermanentDown($query) {
        return $query->where('status', Status::PRODUCT_PERMANENT_DOWN);
    }
    public function scopeWaiting($query) {
        return $query->whereIn('status', [Status::PRODUCT_PENDING, Status::PRODUCT_UPDATE_PENDING]);
    }

    public function changelogs() {
        return $this->hasMany(Changelog::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function subcategory() {
        return $this->belongsTo(Subcategory::class);
    }


    public function productData() {
        return $this->hasMany(ProductData::class, 'product_id', 'id');
    }

    public function options() {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeOptions() {
        return $this->hasMany(ProductOption::class)->where('is_active', Status::YES)->orderBy('sort_order')->orderBy('id');
    }

    public function files() {
        return $this->hasMany(ProductFile::class)->orderBy('sort_order')->orderBy('id');
    }

    public function activeFiles() {
        return $this->hasMany(ProductFile::class)->where('is_active', Status::YES)->orderBy('sort_order')->orderBy('id');
    }

    public function reviews() {
        return $this->hasMany(Review::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function activities() {
        return $this->hasMany(Activity::class);
    }

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function collections() {
        return $this->belongsToMany(ProductCollection::class, 'collection_product');
    }

    public function rejections() {
        return $this->hasMany(Rejection::class);
    }

    public function downloadLogs() {
        return $this->hasMany(DownloadLog::class, 'product_id');
    }

    public function earnings() {
        return $this->hasMany(Earning::class, 'product_id');
    }

    public function screenshots() {
        $slug          = $this->slug;
        $extractedPath = getFilePath('screenshots') . '/' . $slug . '/screenshots';

        if (!is_dir($extractedPath)) {
            return [];
        }

        $files = File::allFiles($extractedPath);

        return collect($files)->map(function ($file) use ($extractedPath) {
            return $extractedPath . '/' . $file->getRelativePathname();
        });
    }

    public function updateStatusBadge(): Attribute {
        return new Attribute(function () {
            $html = '';
            if ($this->product_updated == Status::PRODUCT_UPDATE_PENDING) {
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            } else if ($this->product_updated == Status::PRODUCT_UPDATE_APPROVED) {
                $html = '<span class="badge badge--success">' . trans('Approved') . '</span>';
            } else if ($this->product_updated == Status::PRODUCT_UPDATE_SOFT_REJECT) {
                $html = '<span class="badge badge--warning">' . trans('Soft Rejected') . '</span>';
            } else if ($this->product_updated == Status::PRODUCT_UPDATE_HARD_REJECT) {
                $html = '<span class="badge badge--danger">' . trans('Hard Rejected') . '</span>';
            } else if ($this->product_updated == Status::PRODUCT_NO_UPDATE) {
                $html = '<span class="badge bg-secondary">' . trans('No Update') . '</span>';
            }
            return $html;
        });
    }

    public function statusBadge(): Attribute {
        return new Attribute(function () {
            $html = '';
            if ($this->status == Status::PRODUCT_PENDING) {
                $html = '<span class="badge badge--warning">' . trans('Pending') . '</span>';
            } else if ($this->status == Status::PRODUCT_APPROVED) {
                $html = '<span class="badge badge--success">' . trans('Approved') . '</span>';
            } else if ($this->status == Status::PRODUCT_SOFT_REJECTED) {
                $html = '<span class="badge badge--warning">' . trans('Soft Rejected') . '</span>';
            } else if ($this->status == Status::PRODUCT_HARD_REJECTED) {
                $html = '<span class="badge badge--danger">' . trans('Hard Rejected') . '</span>';
            } else if ($this->status == Status::PRODUCT_DOWN) {
                $html = '<span class="badge badge--warning">' . trans('Soft Disabled') . '</span>';
            } else if ($this->status == Status::PRODUCT_PERMANENT_DOWN) {
                $html = '<span class="badge badge--danger">' . trans('Permanent Disabled') . '</span>';
            }
            return $html;
        });
    }

    public function getCatalogActionLabelAttribute() {
        return $this->hasActiveOptions() ? 'Select options' : 'Buy now';
    }

    public function getCatalogPriceLabelAttribute() {
        if (!$this->managed_by_admin) {
            return $this->is_free ? trans('Free') : null;
        }

        $options = $this->relationLoaded('activeOptions') ? $this->activeOptions : $this->activeOptions()->get();

        if ($options->isNotEmpty()) {
            $min = $options->min('price');
            $max = $options->max('price');

            if ((float) $min === (float) $max) {
                return showAmount($min);
            }

            return showAmount($min) . ' - ' . showAmount($max);
        }

        return showAmount($this->base_price ?? 0);
    }

    public function hasActiveOptions(): bool {
        if ($this->relationLoaded('activeOptions')) {
            return $this->activeOptions->isNotEmpty();
        }

        return $this->activeOptions()->exists();
    }

    public function visibleFilesForOption($optionId = null) {
        return $this->activeFiles()
            ->when($optionId, function ($query) use ($optionId) {
                $query->where(function ($fileQuery) use ($optionId) {
                    $fileQuery->whereNull('product_option_id')->orWhere('product_option_id', $optionId);
                });
            }, function ($query) {
                $query->whereNull('product_option_id');
            });
    }



}
