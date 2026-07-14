@extends('Template::layouts.master')
@section('content')
    <div class="row justify-content-center gy-3">
        <div class="col-12">
            <div class="d-flex align-items justify-content-between flex-wrap gap-2">
                <h6 class="mb-0">{{ __($pageTitle) }}</h6>
                <x-search-form inputClass="form--control search" btn="btn--base btn--sm" placeholder="Search by plan" />
            </div>
        </div>
        <div class="col-md-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if ($subscriptions->count() == 0)
                        <x-empty-list title="No subscription history" />
                    @else
                        <div class="table-responsive">
                            <table class="table table--responsive--md">
                                <thead>
                                    <tr>
                                        <th>@lang('Plan')</th>
                                        <th class="text-center">@lang('Duration')</th>
                                        <th class="text-center">@lang('Price')</th>
                                        <th class="text-center">@lang('Daily | Weekly | Monthly')</th>
                                        <th class="text-center">@lang('Payment')</th>
                                        <th class="text-center">@lang('Source')</th>
                                        <th class="text-center">@lang('TRX')</th>
                                        <th>@lang('Status')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($subscriptions ?? [] as $item)
                                        <tr>
                                            <td>
                                                {{ __($item->plan?->name ?? '') }}
                                            </td>
                                            <td class="text-end text-md-center">
                                                {{ $item->plan_duration == Status::MONTHLY_PLAN ? __('Monthly') : __('Yearly') }}
                                            </td>

                                            <td class="text-end text-md-center">
                                                {{ showAmount($item->price) }}
                                            </td>
                                            <td class="text-end text-md-center">
                                                <span class="badge badge--success" data-bs-toggle="tooltip"
                                                    title="@lang('Daily Limit :limit', ['limit' => $item->daily_limit])">{{ $item->daily_limit }}</span> <span
                                                    class="badge badge--primary" data-bs-toggle="tooltip"
                                                    title="@lang('Weekly Limit :limit', ['limit' => $item->weekly_limit])">{{ $item->weekly_limit }}</span> <span
                                                    class="badge badge--base" data-bs-toggle="tooltip"
                                                    title="@lang('Monthly Limit :limit', ['limit' => $item->monthly_limit])"> {{ $item->monthly_limit }}</span>
                                            </td>
                                            <td class="text-center">
                                                @php echo $item->paymentBadge @endphp
                                            </td>
                                            <td class="text-center">
                                                @php echo $item->paymentSourceBadge @endphp
                                            </td>
                                            <td class="text-end text-md-center">
                                                {{ $item->payment_trx ?? '—' }}
                                            </td>
                                            <td>
                                                @php echo $item->statusBadge @endphp
                                            </td>
                                            <td>
                                                @if ($item->is_payment == Status::UNPAID_SUBSCRIPTION)
                                                    <a href="{{ route('user.payment', encrypt($item->id)) }}" class="btn btn--base btn--sm">@lang('Pay')</a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                @if ($subscriptions->hasPages())
                    <div class="py-2">
                        <div class="card-footer">
                            {{ paginateLinks($subscriptions) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
