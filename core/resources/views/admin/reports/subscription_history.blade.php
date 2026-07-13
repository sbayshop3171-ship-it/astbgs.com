@extends('admin.layouts.app')

@section('panel')
    <div class="row">

        <div class="col-lg-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn-outline--primary showFilterBtn btn-sm"><i class="las la-filter"></i>@lang('Filter')</button>
            </div>
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Plan')</label>
                                <select class="form-control select2" data-minimum-results-for-search="-1" name="plan_id">
                                    <option value="">@lang('All')</option>
                                    @foreach ($plans as $plan)
                                        <option value="{{ $plan->id }}" @selected(request()->plan_id == $plan->id)>
                                            {{ __(keyToTitle($plan->name)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Plan Duration')</label>
                                <select class="form-control select2" data-minimum-results-for-search="-1" name="plan_duration">
                                    <option value="">@lang('All')</option>
                                    <option value="1" @selected(request()->plan_duration == Status::MONTHLY_PLAN)>@lang('Monthly')</option>
                                    <option value="2" @selected(request()->plan_duration == Status::YEARLY_PLAN)>@lang('Yearly')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="search" class="datepicker-here form-control bg--white pe-2 date-range" placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i>
                                    @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('User')</th>
                                    <th>@lang('Plan')</th>
                                    <th>@lang('Duration')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Download Limit')</th>
                                    <th>@lang('Payment Status')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($subscriptions ?? [] as $item)
                                    <tr>
                                        <td>
                                            {{ $item->user?->fullname }}
                                        </td>
                                        <td>
                                            {{ __($item->plan?->name ?? '') }}
                                        </td>
                                        <td>
                                            {{ $item->plan_duration == Status::MONTHLY_PLAN ? __('Monthly') : __('Yearly') }}
                                        </td>
                                        <td>
                                            {{ showAmount($item->price) }}
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block text--small">@lang('Daily'): {{ $item->daily_limit }}</span>
                                                <span class="d-block text--small">@lang('Weekly'): {{ $item->weekly_limit }}</span>
                                                <span class="d-block text--small">@lang('Monthly'): {{ $item->monthly_limit }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            @php echo $item->paymentBadge @endphp
                                        </td>

                                        <td>
                                            @php echo $item->statusBadge @endphp
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($subscriptions->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($subscriptions) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/css/daterangepicker.css') }}">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            const datePicker = $('.date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                showDropdowns: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                        .endOf('month')
                    ],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment()
            });
            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
            }


            $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker
                .startDate, picker.endDate));


            if ($('.date-range').val()) {
                let dateRange = $('.date-range').val().split(' - ');
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }

        })(jQuery)
    </script>
@endpush
