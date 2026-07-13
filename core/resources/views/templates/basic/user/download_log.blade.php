@extends('Template::layouts.master')
@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-12">
            <div class="d-flex align-items justify-content-between flex-wrap gap-2">
                <h6 class="mb-0">@lang('Downloaded Items')</h6>
                <x-search-form inputClass="form--control search" btn="btn--base btn--sm" placeholder="Search by product" />
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if ($downloads->count() == 0)
                        <x-empty-list title="No downloads" />
                    @else
                        <div class="table-responsive">
                            <table class="table table--responsive--md">
                                <thead>
                                    <tr>
                                        <th>@lang('Item')</th>
                                        <th>@lang('Category')</th>
                                        <th>@lang('User')</th>
                                        <th>@lang('Level Commission')</th>
                                        <th>@lang('Earning')</th>
                                        <th>@lang('Total Download')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($downloads ?? [] as $download)
                                        @php
                                            $commission = $download->product->category->author_commission ?? 0;
                                            $levelEarning = $download->earning - $commission;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="table-product flex-align">
                                                    <div class="table-product__thumb">
                                                        <x-product-thumbnail :product="$download->product ?? null" />
                                                    </div>

                                                    <div class="table-product__content">
                                                        @if ($download?->product)
                                                            <a href="{{ route('product.details', $download->product?->slug) }}"
                                                                class="table-product__name text--base">
                                                                {{ __(strLimit($download->product?->title, 15)) }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td> <b>{{ __($download->product?->category?->name) }}</b> <br>
                                                <small>@lang('Commission:') {{ showAmount($commission) }}</small>
                                            </td>
                                            <td>{{ $download->user->fullname }}</td>
                                            <td>
                                                {{ showAmount($download->earning > 0 ? $levelEarning : 0) }}
                                            </td>
                                            <td>{{ showAmount($download->earning) }}</td>
                                            <td><span class="badge badge--base">{{ $download?->download_count ?? 0 }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                @if ($downloads->hasPages())
                    <div class="py-2">
                        <div class="card-footer">
                            {{ paginateLinks($downloads) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
