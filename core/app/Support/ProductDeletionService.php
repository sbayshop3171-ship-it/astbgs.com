<?php

namespace App\Support;

use App\Constants\Status;
use App\Models\Comment;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ProductDeletionService
{
    public function delete(Product $product): void
    {
        $product->loadMissing(['reviews.reportDetails.attachments']);

        if ($this->hasProtectedHistory($product)) {
            $this->archiveProduct($product);
            return;
        }

        $directories = $this->productDirectories($product);
        $reviewAttachments = $this->reviewAttachmentFiles($product);

        DB::transaction(function () use ($product) {
            $product->users()->detach();
            $product->collections()->detach();

            Comment::where('product_id', $product->id)->delete();

            foreach ($product->reviews as $review) {
                if ($review->reportDetails) {
                    $review->reportDetails->attachments()->delete();
                    $review->reportDetails->delete();
                }

                $review->delete();
            }

            $product->activities()->delete();
            $product->changelogs()->delete();
            $this->deleteOptionalRelation('product_data', fn() => $product->productData()->delete());
            $product->downloadLogs()->delete();
            $product->earnings()->delete();
            $product->rejections()->delete();
            $product->files()->delete();
            $product->options()->delete();

            $this->deleteOptionalRelation('product_views', fn() => ProductView::where('product_id', $product->id)->delete());

            $product->delete();
        });

        $this->cleanupFilesystem($directories, $reviewAttachments);
    }

    protected function hasProtectedHistory(Product $product): bool
    {
        return OrderItem::where('product_id', $product->id)->exists()
            || $product->downloadLogs()->exists()
            || $product->earnings()->exists();
    }

    protected function productDirectories(Product $product): array
    {
        return [
            getFilePath('productThumbnail') . '/' . $product->slug,
            getFilePath('productPreview') . '/' . $product->slug,
            getFilePath('productInlinePreview') . '/' . $product->slug,
            getFilePath('productFile') . '/' . $product->slug,
            getFilePath('screenshots') . '/' . $product->slug,
        ];
    }

    protected function reviewAttachmentFiles(Product $product): array
    {
        return $product->reviews
            ->flatMap(function ($review) {
                return $review->reportDetails?->attachments?->pluck('attachment') ?? collect();
            })
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function archiveProduct(Product $product): void
    {
        DB::transaction(function () use ($product) {
            $product->users()->detach();
            $product->collections()->detach();

            $product->status = Status::PRODUCT_PERMANENT_DOWN;

            if (array_key_exists('is_published', $product->getAttributes())) {
                $product->is_published = Status::NO;
            }

            if (array_key_exists('availability_status', $product->getAttributes())) {
                $product->availability_status = Status::PRODUCT_AVAILABILITY_UNAVAILABLE;
            }

            $product->save();
        });
    }

    protected function deleteOptionalRelation(string $table, callable $callback): void
    {
        if (!Schema::hasTable($table)) {
            return;
        }

        $callback();
    }

    protected function cleanupFilesystem(array $directories, array $reviewAttachments): void
    {
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            try {
                fileManager()->removeDirectory($directory);
            } catch (\Throwable $exception) {
                Log::warning('Product directory cleanup failed', [
                    'path' => $directory,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $reviewAttachmentPath = getFilePath('reviewReport');

        foreach ($reviewAttachments as $attachment) {
            $path = $reviewAttachmentPath . '/' . $attachment;

            if (!file_exists($path)) {
                continue;
            }

            try {
                fileManager()->removeFile($path);
            } catch (\Throwable $exception) {
                Log::warning('Product review attachment cleanup failed', [
                    'path' => $path,
                    'error' => $exception->getMessage(),
                ]);
            }
        }
    }
}
