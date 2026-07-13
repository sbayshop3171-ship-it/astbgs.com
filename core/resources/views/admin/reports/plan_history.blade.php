@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Plan')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Remark')</th>
                                    <th>@lang('Created at')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($histories as $history)
                                    <tr>
                                        <td>{{ __($history->plan->name) }}</td>
                                        <td>
                                            <span
                                                class="fw-bold @if ($history->history_type == '+') text--success @else text--danger @endif">
                                                {{ $history->history_type }} 
                                            </span>{{ showAmount($history->amount) }}
                                        </td>
                                        <td> {{ __(keyToTitle($history->remark)) }}</td>
                                        <td>{{ showDateTime($history->created_at) }}</td>
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
                @if ($histories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($histories) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <x-search-form placeholder="Plan" dateSearch='yes' />
@endpush
