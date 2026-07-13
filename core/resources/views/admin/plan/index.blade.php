@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Download Limit')</th>
                                    <th>@lang('Is Popular')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody class="list">
                                @forelse($plans as $plan)
                                    <tr>
                                        <td class="fw-bold"> {{ __($plan->name) }} </td>
                                        <td>
                                            <div>
                                                <span class="d-block mb-1">@lang('Monthly'): {{ showAmount($plan->monthly_price) }}</span>
                                                <span class="d-block">@lang('Yearly'): {{ showAmount($plan->yearly_price) }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="d-block text--small">@lang('Daily'): {{ $plan->daily_limit }}</span>
                                                <span class="d-block text--small">@lang('Weekly'): {{ $plan->weekly_limit }}</span>
                                                <span class="d-block text--small">@lang('Monthly'): {{ $plan->monthly_limit }}</span>
                                            </div>
                                        </td>

                                        <td> @php echo $plan->statusPopular; @endphp</td>
                                        <td> @php echo $plan->statusBadge; @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.plan.form', $plan->id) }}" class="btn btn-sm btn-outline--primary"><i class="las la-pencil-alt"></i>@lang('Edit')</a>

                                                <button class="btn btn-outline--dark btn-sm dropdown-toggle" data-bs-toggle="dropdown"><i class="las la-ellipsis-v"></i>@lang('More')</button>
                                                <ul class="dropdown-menu px-2">
                                                    @if ($plan->is_popular == Status::DISABLE)
                                                        <li>
                                                            <a href="javascript:void(0)" class="dropdown-item confirmationBtn" data-action="{{ route('admin.plan.popular', $plan->id) }}" data-question="@lang('Are you sure you want to mark this plan as popular?')">
                                                                <i class="las la-heart"></i> @lang('Mark As Popular')
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a href="javascript:void(0)" class="dropdown-item confirmationBtn" data-action="{{ route('admin.plan.popular', $plan->id) }}" data-question="@lang('Are you sure to remove the plan from the popular plan?')">
                                                                <i class="lab la-gratipay"></i> @lang('Unmark Popular')
                                                            </a>
                                                        </li>
                                                    @endif

                                                    @if ($plan->status == Status::DISABLE)
                                                        <li>
                                                            <a href="javascript:void(0)" class="dropdown-item confirmationBtn" data-action="{{ route('admin.plan.status', $plan->id) }}" data-question="@lang('Are you sure to enable this plan?')">
                                                                <i class="la la-eye"></i> @lang('Enable')
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a href="javascript:void(0)" class="dropdown-item confirmationBtn" data-action="{{ route('admin.plan.status', $plan->id) }}" data-question="@lang('Are you sure to disable this plan?')">
                                                                <i class="la la-eye-slash"></i> @lang('Disable')
                                                            </a>
                                                        </li>
                                                    @endif

                                                    <li>
                                                        <a href="{{ route('admin.report.subscription.history', ['plan_id' =>$plan->id]) }}" class="dropdown-item"><i class="las la-chart-bar"></i> @lang('Subscriptions')
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
                @if ($plans->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($plans) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search by plan" />
    <a href="{{ route('admin.plan.form') }}" class="btn btn-outline--primary"><i class="las la-plus"></i>@lang('Add New')</a>
@endpush
