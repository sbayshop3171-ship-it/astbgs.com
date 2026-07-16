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

    public static function find(string $cartKey): ?array {
        return self::items()->get($cartKey);
    }

    public static function cartKeyFor(int $productId, ?int $optionId = null, $requestedAmount = null): string {
        $normalizedAmount = filled($requestedAmount) ? (string) (float) $requestedAmount : '';

        return md5($productId . ':' . ($optionId ?? 'base') . ':' . $normalizedAmount);
    }

    public static function findForProduct(int $productId, ?int $optionId = null, $requestedAmount = null): ?array {
        $cartKey = self::cartKeyFor($productId, $optionId, $requestedAmount);

        return self::find($cartKey);
    }

    public static function add(Product $product, ?ProductOption $option = null, int $quantity = 1, ?string $requestNote = null, $requestedAmount = null): array {
        $items = self::items();
        $cartKey = self::cartKeyFor($product->id, $option?->id, $requestedAmount);
        $unitPrice = $option ? (float) $option->price : (float) ($product->base_price ?? 0);
        $existingItem = $items->get($cartKey, []);
        $existingQuantity = (int) ($existingItem['quantity'] ?? 0);

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
            'quantity'          => min(99, max(1, $quantity) + $existingQuantity),
            'request_note'      => $requestNote ?? ($existingItem['request_note'] ?? null),
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

    public static function setQuantity(string $cartKey, int $quantity, array $attributes = []): ?array {
        if ($quantity <= 0) {
            self::remove($cartKey);

            return null;
        }

        $items = self::items();
        if (!$items->has($cartKey)) {
            return null;
        }

        $item = $items->get($cartKey);
        $item['quantity'] = min(99, max(1, $quantity));
        $item['request_note'] = $attributes['request_note'] ?? $item['request_note'];
        $item['requested_amount'] = filled($attributes['requested_amount'] ?? null) ? (float) $attributes['requested_amount'] : $item['requested_amount'];

        $items->put($cartKey, $item);
        session()->put(self::SESSION_KEY, $items->all());

        return $item;
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
