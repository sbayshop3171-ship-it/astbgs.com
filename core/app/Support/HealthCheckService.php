<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class HealthCheckService
{
    public function run(): array
    {
        return [
            'database'       => $this->checkDatabase(),
            'catalog_schema' => $this->checkCatalogSchema(),
            'catalog_query'  => $this->checkCatalogQuery(),
            'storage'        => $this->checkStorage(),
        ];
    }

    protected function checkDatabase(): string
    {
        DB::connection()->getPdo();
        DB::select('select 1');

        return 'ok';
    }

    protected function checkCatalogSchema(): string
    {
        foreach (['products', 'product_options', 'product_files', 'orders', 'order_items'] as $table) {
            if (!Schema::hasTable($table)) {
                throw new RuntimeException("catalog_schema: missing required table [$table].");
            }
        }

        foreach (['product_type', 'managed_by_admin', 'is_published', 'availability_status', 'base_price'] as $column) {
            if (!Schema::hasColumn('products', $column)) {
                throw new RuntimeException("catalog_schema: missing required column [products.$column].");
            }
        }

        if (!Schema::hasColumn('deposits', 'order_id')) {
            throw new RuntimeException('catalog_schema: missing required column [deposits.order_id].');
        }

        return 'ok';
    }

    protected function checkCatalogQuery(): string
    {
        DB::table('products')->select('id')->limit(1)->get();
        DB::table('orders')->select('id')->limit(1)->get();

        return 'ok';
    }

    protected function checkStorage(): string
    {
        foreach ([storage_path('framework'), storage_path('logs')] as $path) {
            if (!is_dir($path)) {
                throw new RuntimeException("storage: missing directory [$path].");
            }

            if (!is_writable($path)) {
                throw new RuntimeException("storage: directory not writable [$path].");
            }
        }

        return 'ok';
    }
}
