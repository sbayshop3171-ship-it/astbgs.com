@extends('Template::layouts.master')
@section('content')
    <div class="row gy-3">
        <div class="col-sm-3">
            <div class="dashboard-widget">
                <span class="dashboard-widget__icon--big"><i class="la la-chart-line"></i></span>
                <h6 class="dashboard-widget__title">@lang('Total Referral Earnings')</h6>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__icon"><i class="la la-chart-line"></i></span>
                    <div class="dashboard-widget__info">
                        <h5 class="dashboard-widget__amount">{{ showAmount($referral['total']) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="dashboard-widget">
                <span class="dashboard-widget__icon--big"><i class="la la-calendar-day"></i></span>
                <h6 class="dashboard-widget__title">@lang('Today Referral Earning')</h6>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__icon"><i class="la la-calendar-day"></i></span>
                    <div class="dashboard-widget__info">
                        <h5 class="dashboard-widget__amount">{{ showAmount($referral['today']) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="dashboard-widget text--white">
                <span class="dashboard-widget__icon--big"><i class="la la-calendar-week"></i></span>
                <h6 class="dashboard-widget__title">@lang('This Week Referral Earning')</h6>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__icon"><i class="la la-calendar-week"></i></span>
                    <div class="dashboard-widget__info">
                        <h5 class="dashboard-widget__amount">{{ showAmount($referral['week']) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-3">
            <div class="dashboard-widget">
                <span class="dashboard-widget__icon--big"><i class="la la-calendar-alt"></i></span>
                <h6 class="dashboard-widget__title">@lang('This Month Referral Earning')</h6>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__icon"><i class="la la-calendar-alt"></i></span>
                    <div class="dashboard-widget__info">
                        <h5 class="dashboard-widget__amount">{{ showAmount($referral['month']) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card custom--card">
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label" data-bs-toggle="tooltip" data-bs-placement="top" title="Refer others to earn rewards and commissions on their membership!">
                            @lang('Referral link') <i class="fas fa-info-circle"></i>
                        </label>

                        @php
                            $referralLink = route('home') . '?reference=' . auth()->user()->username;
                        @endphp

                        <div class="input-group">
                            <input type="text" name="key" value="{{ $referralLink }}" class="form-control form--control referralURL" readonly>
                            <button type="button" class="input-group-text btn btn--base px-3 copytext" id="copyBoard">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-2 gap-3">
                    <h6 class="mb-0">@lang('My Referral Earnings')</h6>
                    <div class="d-flex justify-content-end align-items-center gap-3 flex-wrap mb-2">
                        <x-search-form inputClass="form--control  search" btn="btn--base btn--sm" placeholder="Username/TRX" />
                        <a href="{{ route('user.referred.users') }}" class="btn btn--dark"><i class="fa fa-list"></i> @lang('Referred Users')</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card custom--card">
                    <div class="card-body p-0">
                        @if ($referralEarnings->count() > 0)
                            <div class="table-responsive">
                                <table class="table table--responsive--xl">
                                    <thead>
                                        <tr>
                                            <th>@lang('Trx')</th>
                                            <th>@lang('Transacted')</th>
                                            <th>@lang('Amount')</th>
                                            <th>@lang('Post Balance')</th>
                                            <th>@lang('Detail')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($referralEarnings as $trx)
                                            <tr>
                                                <td><strong>{{ $trx->trx }}</strong></td>
                                                <td>{{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                                </td>
                                                <td class="budget">
                                                    <span class="fw-bold @if ($trx->trx_type == '+') text--success @else text-danger @endif">
                                                        {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                                    </span>
                                                </td>
                                                <td class="budget">
                                                    {{ showAmount($trx->post_balance) }}
                                                </td>
                                                <td>{{ __($trx->details) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if ($referralEarnings->hasPages())
                                <div class="card-footer">
                                    <div class="py-2">
                                        {{ paginateLinks($referralEarnings) }}
                                    </div>
                                </div>
                            @endif
                        @else
                            <x-empty-list title="No referral earning found" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('#copyBoard').on('click', function() {
                var copyText = document.getElementsByClassName("referralURL");
                copyText = copyText[0];
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                /*For mobile devices*/
                document.execCommand("copy");
                copyText.blur();
                this.classList.add('copied');
                setTimeout(() => this.classList.remove('copied'), 15000);
            });
        })(jQuery);
    </script>
@endpush
