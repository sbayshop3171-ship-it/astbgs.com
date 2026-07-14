@extends('Template::layouts.master')
@section('content')
    @php
        $kyc = getContent('kyc.content', true);
    @endphp

    @if (auth()->check() && $author->isAuthor())
        @if ($pendingProducts)
            <x-alert type="warning" icon="spinner" title="Pending Items"
                route="{{ route('user.author.hidden.items', ['status' => Status::PRODUCT_PENDING]) }}">
                @lang('You have') {{ $pendingProducts }} @lang('pending')
                {{ __(str()->plural('item', $pendingProducts)) }}
            </x-alert>
        @endif
        @if ($downProducts)
            <x-alert type="dark" icon="level-down-alt" title="Down Items"
                route="{{ route('user.author.hidden.items', ['status' => Status::PRODUCT_DOWN]) }}">
                @lang('You have') {{ $downProducts }} @lang('down') {{ __(str()->plural('item', $downProducts)) }}
            </x-alert>
        @endif

        @if ($softRejectedProducts)
            <x-alert type="danger" icon="ban" title="Reject Items"
                route="{{ route('user.author.hidden.items', ['status' => Status::PRODUCT_SOFT_REJECTED]) }}">
                @lang('You have') {{ $softRejectedProducts }} @lang('soft rejected')
                {{ __(str()->plural('script', $softRejectedProducts)) }}
            </x-alert>
        @endif
        @if ($unRepliedComments)
            <x-alert type="warning" icon="comment-slash" title="Unreplied Comments"
                route="{{ route('user.author.comments.index', ['not_replied' => 1]) }}">
                @lang('You have') {{ $unRepliedComments }} @lang('unreplied')
                {{ __(str()->plural('comment', $unRepliedComments)) }}
            </x-alert>
        @endif

        @if ($unRepliedReviews)
            <x-alert type="info" icon="comment-slash" title="Unreplied Reviews"
                route="{{ route('user.author.reviews.index', ['not_replied' => 1]) }}">
                @lang('You have') {{ $unRepliedReviews }} @lang('unreplied')
                {{ __(str()->plural('review', $unRepliedReviews)) }}
            </x-alert>
        @endif
    @endif

    <div class="row gy-4 dashboard-row-wrapper">
        <div class="notice"></div>
        @if ($author->kv == Status::KYC_UNVERIFIED && $author->kyc_rejection_reason)
            <div class="col-12">
                <div class="alert alert--danger" role="alert">
                    <div class="alert__icon">
                        <i class="la la-ban"></i>
                    </div>
                    <div class="alert__content">
                        <h6 class="alert__title">@lang('KYC Documents Rejected')</h6>
                        {{ __($kyc?->data_values?->reject) }}
                        <a href="javascript::void(0)" class="link-color" data-bs-toggle="modal"
                            data-bs-target="#kycRejectionReason">@lang('Click here')</a> @lang('to show the reason').

                        <a href="{{ route('user.kyc.form') }}">@lang('Click Here to Re-submit Documents')</a>.
                        <br>
                        <a href="{{ route('user.kyc.data') }}">@lang('See KYC Data')</a>
                        <button class="btn btn-outline--secondary btn--sm" data-bs-toggle="modal"
                            data-bs-target="#kycRejectionReason">@lang('Show Reason')</button>
                    </div>
                </div>
            </div>
        @elseif($author->kv == Status::KYC_UNVERIFIED)
            <div class="col-12">
                <div class="alert alert--info" role="alert">
                    <div class="alert__icon">
                        <i class="la la-yin-yang"></i>
                    </div>
                    <div class="alert__content">
                        <h6 class="alert__title">@lang('KYC Documents required')</h6>
                        {{ __($kyc?->data_values?->required) }} <a
                            href="{{ route('user.kyc.form') }}">@lang('Click Here to Submit Documents')</a>
                    </div>
                </div>
            </div>
        @elseif($author->kv == Status::KYC_PENDING)
            <div class="col-12">
                <div class="alert alert--warning" role="alert">
                    <div class="alert__icon">
                        <i class="la la-spinner"></i>
                    </div>
                    <div class="alert__content">
                        <h6 class="alert__title">@lang('KYC Documents pending')</h6>
                        {{ __($kyc?->data_values?->pending) }} <a
                            href="{{ route('user.kyc.data') }}">@lang('See KYC Data')</a>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-12">
            @include('Template::user.dashboard.widgets')
        </div>

        @if (!$author->is_author)
            <div class="col-12">
                <div class="card product-card">
                    <div class="card-body p-4">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="text-center">
                                    <h3 class="text--base">@lang('Welcome to Your Account')</h3>
                                    <p class="mb-3">@lang('Browse admin-managed catalog items, place orders, and track payments and downloads from one place.')</p>
                                    <div class="d-flex justify-content-center flex-wrap gap-2">
                                        <a href="{{ route('products') }}" class="btn btn--base">@lang('Browse Catalog')</a>
                                        <a href="{{ route('user.orders.index') }}" class="btn btn-outline--base">@lang('My Orders')</a>
                                        <a href="{{ route('user.deposit.index') }}" class="btn btn-outline--light">@lang('Add Money')</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('Template::user.dashboard.recent_wallet_activity')
        @endif

        @include('Template::user.dashboard.recent_downloads')
    </div>

    @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
        <div class="modal fade" id="kycRejectionReason">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ auth()->user()->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
