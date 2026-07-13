@php
    $user = $author;
    $bsColClass = $user->is_author ? 'col-lg-3' : 'col-lg-4';
    $currentLevel = $user->currentAuthorLevel->first();
@endphp

<div class="row gy-3">
    <div class="{{ $bsColClass }} col-sm-6">
        <div class="dashboard-widget">
            <span class="dashboard-widget__icon--big"><i class="la la-database"></i></span>
            <h6 class="dashboard-widget__title">@lang('Author Level')</h6>
            <div class="dashboard-widget__content">
                <span class="dashboard-widget__icon"><i class="la la-database"></i></span>
                <div class="dashboard-widget__info">
                    <h5 class="dashboard-widget__amount"> {{ __($currentLevel?->name ?? 'N/A') }} </h5>
                </div>
            </div>
        </div>
    </div>
    <div class="{{ $bsColClass }} col-sm-6">
        <div class="dashboard-widget">
            <span class="dashboard-widget__icon--big"><i class="icon-money-bag-Icon"></i></span>
            <h6 class="dashboard-widget__title">@lang('Balance')</h6>
            <div class="dashboard-widget__content">
                <span class="dashboard-widget__icon"><i class="icon-money-bag-Icon"></i></span>
                <div class="dashboard-widget__info">
                    <h5 class="dashboard-widget__amount">{{ showAmount($user->balance) }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="{{ $bsColClass }} col-sm-6">
        <div class="dashboard-widget">
            <span class="dashboard-widget__icon--big"><i class="la la-coins"></i></span>
            <h6 class="dashboard-widget__title">@lang('Total Earning By Download')</h6>
            <div class="dashboard-widget__content">
                <span class="dashboard-widget__icon"><i class="la la-coins"></i></span>
                <div class="dashboard-widget__info">
                    <h5 class="dashboard-widget__amount">{{ showAmount($downloadCommissionAmount) }}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="{{ $bsColClass }} col-sm-6">
        <div class="dashboard-widget">
            <span class="dashboard-widget__icon--big"><i class="la la-bank"></i></span>
            <h6 class="dashboard-widget__title">@lang('Withdrawals')</h6>
            <div class="dashboard-widget__content">
                <span class="dashboard-widget__icon"><i class="la la-bank"></i></span>
                <div class="dashboard-widget__info">
                    <h5 class="dashboard-widget__amount">{{ showAmount($totalWithdrawals) }}
                    </h5>
                </div>
            </div>
        </div>
    </div>
</div>
