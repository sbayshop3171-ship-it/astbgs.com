<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Support\Collection;

class CatalogCart {
    public const SESSION_KEY = 'catalog_cart';

    public static function items(): Collection {
        return collect(session()->get(self::SESSION_KEY, []));
    }

    public static function all(): array {
        return self::items()->values()->all();
    }

    public static function add(Product $product, ?ProductOption $option = null, int $quantity = 1, ?string $requestNote = null, $requestedAmount = null): array {
        $items = self::items();
        $normalizedAmount = filled($requestedAmount) ? (string) (float) $requestedAmount : '';
        $cartKey = md5($product->id . ':' . ($option?->id ?? 'base') . ':' . $normalizedAmount);
        $unitPrice = $option ? (float) $option->price : (float) ($product->base_price ?? 0);

        $item = [
            'cart_key'          => $cartKey,
            'product_id'        => $product->id,
            'slug'              => $product->slug,
            'title'             => $product->title,
            'thumbnail'         => $product->thumbnail,
            'product_type'      => $product->product_type,
            'delivery_type'     => $product->product_type,
            'product_option_id' => $option?->id,
            'option_name'       => $option?->name,
            'unit_price'        => $unitPrice,
            'quantity'          => max(1, $quantity),
            'request_note'      => $requestNote,
            'requested_amount'  => filled($requestedAmount) ? (float) $requestedAmount : null,
            'availability_note' => $option?->availability_note,
            'min_amount'        => $option?->min_amount,
            'max_amount'        => $option?->max_amount,
            'is_downloadable'   => $product->product_type === Status::PRODUCT_TYPE_DOWNLOADABLE,
        ];

        $items->put($cartKey, $item);
        session()->put(self::SESSION_KEY, $items->all());

        return $item;
    }

    public static function update(string $cartKey, array $attributes): void {
        $items = self::items();
        if (!$items->has($cartKey)) {
            return;
        }

        $item = $items->get($cartKey);
        $item['quantity'] = max(1, (int) ($attributes['quantity'] ?? $item['quantity']));
        $item['request_note'] = $attributes['request_note'] ?? $item['request_note'];
        $item['requested_amount'] = filled($attributes['requested_amount'] ?? null) ? (float) $attributes['requested_amount'] : null;
        $items->put($cartKey, $item);

        session()->put(self::SESSION_KEY, $items->all());
    }

    public static function remove(string $cartKey): void {
        $items = self::items();
        $items->forget($cartKey);
        session()->put(self::SESSION_KEY, $items->all());
    }

    public static function clear(): void {
        session()->forget(self::SESSION_KEY);
    }

    public static function subtotal(): float {
        return (float) self::items()->sum(function ($item) {
            return ((float) $item['unit_price']) * ((int) $item['quantity']);
        });
    }

    public static function count(): int {
        return (int) self::items()->sum('quantity');
    }
}
