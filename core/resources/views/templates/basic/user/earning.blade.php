@extends('Template::layouts.master')
@section('content')
    <div class="row gy-4 dashboard-row-wrapper">
        <div class="col-12">
            <div class="row gy-4">
                <div class="col-lg-4 col-sm-6">
                    <div class="dashboard-widget">
                        <span class="dashboard-widget__icon--big"><i class="fa fa-coins"></i></span>
                        <h6 class="dashboard-widget__title">@lang('Total Earning')</h6>
                        <div class="dashboard-widget__content">
                            <span class="dashboard-widget__icon"><i class="fa fa-coins"></i></span>
                            <div class="dashboard-widget__info">
                                <h5 class="dashboard-widget__amount">{{ showAmount($totalEarning) }}</h5>
                                <span class="dashboard-widget__text">@lang('Your all time earning')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="dashboard-widget">
                        <span class="dashboard-widget__icon--big"><i class="fa fa-calendar-day"></i></span>
                        <h6 class="dashboard-widget__title">@lang('Earning Today')</h6>
                        <div class="dashboard-widget__content">
                            <span class="dashboard-widget__icon"><i class="fa fa-calendar-day"></i></span>
                            <div class="dashboard-widget__info">
                                <h5 class="dashboard-widget__amount">{{ showAmount($todayEarning) }}</h5>
                                <span class="dashboard-widget__text">@lang('Your earning today')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="dashboard-widget">
                        <span class="dashboard-widget__icon--big"><i class="fa fa-calendar-week"></i></span>
                        <h6 class="dashboard-widget__title">@lang('Earning This Week')</h6>
                        <div class="dashboard-widget__content">
                            <span class="dashboard-widget__icon"><i class="fa fa-calendar-week"></i></span>
                            <div class="dashboard-widget__info">
                                <h5 class="dashboard-widget__amount">{{ showAmount($thisWeekEarning) }}</h5>
                                <span class="dashboard-widget__text">@lang('Your earning in this week')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="dashboard-widget">
                        <span class="dashboard-widget__icon--big"><i class="fa fa-calendar-alt"></i></span>
                        <h6 class="dashboard-widget__title">@lang('Earning This Month')</h6>
                        <div class="dashboard-widget__content">
                            <span class="dashboard-widget__icon"><i class="fa fa-calendar-alt"></i></span>
                            <div class="dashboard-widget__info">
                                <h5 class="dashboard-widget__amount">{{ showAmount($thisMonthEarning) }}</h5>
                                <span class="dashboard-widget__text">@lang('Your earning in this month')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="dashboard-widget">
                        <span class="dashboard-widget__icon--big"><i class="fa fa-calendar"></i></span>
                        <h6 class="dashboard-widget__title">@lang('Earning Last 6 Months')</h6>
                        <div class="dashboard-widget__content">
                            <span class="dashboard-widget__icon"><i class="fa fa-calendar"></i></span>
                            <div class="dashboard-widget__info">
                                <h5 class="dashboard-widget__amount">{{ showAmount($lastSixMonthsEarning) }}</h5>
                                <span class="dashboard-widget__text">@lang('Your earning in last 6 months')</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="dashboard-widget">
                        <span class="dashboard-widget__icon--big"><i class="fa fa-calendar"></i></span>
                        <h6 class="dashboard-widget__title">@lang('Earning This Year')</h6>
                        <div class="dashboard-widget__content">
                            <span class="dashboard-widget__icon"><i class="fa fa-calendar"></i></span>
                            <div class="dashboard-widget__info">
                                <h5 class="dashboard-widget__amount">{{ showAmount($thisYearEarning) }}</h5>
                                <span class="dashboard-widget__text">@lang('Your earning in this year')</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
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
                                                <small>@lang('Commission:') {{ showAmount($download->category_commission) }}</small>
                                            </td>
                                            <td>{{ $download->user->fullname }}</td>
                                            <td>
                                                {{ showAmount($download->level_commission) }}
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
