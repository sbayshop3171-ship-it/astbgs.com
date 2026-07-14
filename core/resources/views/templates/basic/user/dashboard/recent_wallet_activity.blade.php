<div class="col-12">
    <div class="row gy-3">
        <div class="col-lg-6">
            <div class="card custom--card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">@lang('Recent Wallet Top-ups')</h6>
                    <a href="{{ route('user.transactions', ['balance_type' => \App\Constants\Status::BALANCE_TYPE_WALLET, 'trx_type' => '+']) }}"
                        class="btn btn-outline--base btn--sm">@lang('View All')</a>
                </div>
                <div class="card-body p-0">
                    @if ($walletTopups->isEmpty())
                        <x-empty-list title="No wallet top-ups found" />
                    @else
                        <div class="table-responsive">
                            <table class="table table--responsive--md">
                                <thead>
                                    <tr>
                                        <th>@lang('Trx')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Date')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($walletTopups as $trx)
                                        <tr>
                                            <td>{{ $trx->trx }}</td>
                                            <td class="text--success">+ {{ showAmount($trx->amount) }}</td>
                                            <td>{{ showDateTime($trx->created_at) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card custom--card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">@lang('Recent Wallet Spends')</h6>
                    <a href="{{ route('user.transactions', ['balance_type' => \App\Constants\Status::BALANCE_TYPE_WALLET, 'trx_type' => '-']) }}"
                        class="btn btn-outline--base btn--sm">@lang('View All')</a>
                </div>
                <div class="card-body p-0">
                    @if ($walletSpends->isEmpty())
                        <x-empty-list title="No wallet spending found" />
                    @else
                        <div class="table-responsive">
                            <table class="table table--responsive--md">
                                <thead>
                                    <tr>
                                        <th>@lang('Trx')</th>
                                        <th>@lang('Amount')</th>
                                        <th>@lang('Date')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($walletSpends as $trx)
                                        <tr>
                                            <td>{{ $trx->trx }}</td>
                                            <td class="text--danger">- {{ showAmount($trx->amount) }}</td>
                                            <td>{{ showDateTime($trx->created_at) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
