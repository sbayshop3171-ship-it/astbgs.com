@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.plan.store', $plan->id ?? null) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>@lang('Name')</label>
                                <input class="form-control" name="name" type="text"
                                    value="{{ old('name', $plan?->name) }}" required />
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Monthly Price')</label>
                                <div class="input-group">
                                    <input class="form-control" name="monthly_price" type="number" step="any"
                                        value="{{ old('monthly_price', getAmount($plan?->monthly_price ?? '')) }}"
                                        required />
                                    <span class="input-group-text">{{ gs('cur_text') }}</span>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Yearly Price')</label>
                                <div class="input-group">
                                    <input class="form-control" name="yearly_price" type="number"
                                        value="{{ old('yearly_price', getAmount($plan->yearly_price ?? '')) }}"
                                        step="any" required />
                                    <span class="input-group-text">{{ gs('cur_text') }}</span>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Daily Limit')</label>
                                <div class="input-group">
                                    <input class="form-control" name="daily_limit" type="number"
                                        value="{{ old('daily_limit', $plan->daily_limit ?? '') }}" required />
                                    <span class="input-group-text">@lang('Items')</span>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Weekly Limit')</label>
                                <div class="input-group">
                                    <input class="form-control" name="weekly_limit" type="number"
                                        value="{{ old('weekly_limit', $plan->weekly_limit ?? '') }}" required />
                                    <span class="input-group-text">@lang('Items')</span>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Monthly Limit')</label>
                                <div class="input-group">
                                    <input class="form-control" name="monthly_limit" type="number"
                                        value="{{ old('monthly_limit', $plan->monthly_limit ?? '') }}" required />
                                    <span class="input-group-text">@lang('Items')</span>
                                </div>
                            </div>
                            <div class="form-group col-md-4">
                                <label>@lang('Save Amount')</label>
                                <div class="input-group">
                                    <input class="form-control" name="save_amount" type="number" step="any"
                                        value="{{ old('save_amount', getAmount($plan->save_amount ?? '')) }}" />
                                    <span class="input-group-text">{{ gs('cur_text') }}</span>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.plan.index') }}" />
@endpush

@push('script')
    <script>
        'use strict';
        (function($) {

            function calculateSaveAmount() {
                let monthly = parseFloat($('input[name="monthly_price"]').val());
                let yearly = parseFloat($('input[name="yearly_price"]').val());

                if (!isNaN(monthly) && !isNaN(yearly)) {
                    let save = (monthly * 12) - yearly;
                    $('input[name="save_amount"]').val(0).attr('readonly', false);
                    if (save < 0) {
                        $('input[name="save_amount"]').val(0).attr('readonly', true);
                        return;
                    }
                    $('input[name="save_amount"]').val(save.toFixed(2));
                }
            }

            function calculateYearlyPrice() {
                let monthly = parseFloat($('input[name="monthly_price"]').val());
                let save = parseFloat($('input[name="save_amount"]').val());
                if (!isNaN(monthly) && !isNaN(save)) {
                    let yearly = (monthly * 12) - save;
                    $('input[name="yearly_price"]').val(0).attr('readonly', false);
                    if (yearly < 0) {
                        $('input[name="yearly_price"]').val(0).attr('readonly', true);
                        return;
                    }
                    $('input[name="yearly_price"]').val(yearly.toFixed(2));
                }
            }

            $('input[name="yearly_price"]').on('input', function() {
                calculateSaveAmount();
            });

            $('input[name="save_amount"]').on('input', function() {
                calculateYearlyPrice();
            });


        })(jQuery)
    </script>
@endpush
