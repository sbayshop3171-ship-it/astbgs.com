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
                                    <th>@lang('Product | Upload At')</th>
                                    <th>@lang('Author')</th>
                                    <th>@lang('Earning')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Total Download')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>
                                            <div class="user d-flex">
                                                <div class="thumb me-2">
                                                    <img src="{{ getImage(getFilePath('productThumbnail') . '/' . productFilePath($log->product, 'thumbnail')) }}"  alt="{{ $log->product->title }}">
                                                </div>
                                                <div>
                                                    <a href="{{ route('product.details', $log->product->slug) }}">{{ __(strLimit($log->product->title, 20)) }}</a>
                                                    <br>
                                                    <span class="text--small">{{ showDateTime($log->product->created_at) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ __($log->author?->fullname) }}</span>
                                            <br>
                                            <span>
                                                <a href="{{ route('admin.users.detail', $log->author->id) }}"><span>@</span>{{ $log->author->username }}</a>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ showAmount($log->earning) }}</span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ $log->user->fullname }}</span>
                                            <br>
                                            <span class="small">
                                                <a
                                                    href="{{ route('admin.users.detail', $log->user_id) }}"><span>@</span>{{ $log->user->username }}</a>
                                            </span>
                                        </td>
                                        <td>
                                           <span class="badge badge--primary"> {{ $log->download_count }}</span>
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
                @if ($logs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($logs) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <x-search-form placeholder="Search Username" dateSearch='yes' />
@endpush
