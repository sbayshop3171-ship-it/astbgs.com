@extends('Template::layouts.frontend')
@section('content')
    @php
        $author = $product->author;
    @endphp
    <section class="product-details pt-60 pb-120">
        <div class="container">
            @include('Template::user.product.top')

            <div class="row gy-4">
                <div class="col-lg-8">
                    <div class="product-response__wrapper">
                        <div id="activity-container" class="product-response__content activity">
                            @if ($activities->count() > 0)
                                <div id="activity-list">
                                    @include('reviewer.partials.activity', compact('activities'))
                                </div>

                                <div id="loader" class="text-center py-3 d-none">
                                    <span class="spinner-border text--primary"></span>
                                </div>
                            @else
                                <div class="w-100 text-center">
                                    <x-empty-list title="This product has no activities" />
                                </div>
                            @endif

                            {{-- @forelse ($activities as $activity)
                                <div class="product-response__item">
                                    <div class="product-response-info mb-2">
                                        <div class="product-response-info__thumb">
                                            @if ($activity->user)
                                                <x-author-avatar :author="$activity->user" />
                                            @else
                                                <img src="{{ getImage(getFilePath('reviewerProfile') . '/' . $activity?->reviewer?->image, getFileSize('reviewerProfile')) }}" alt=")">
                                            @endif
                                        </div>
                                        <div class="product-response-info__content">

                                            <h6 class="product-response-info__name">
                                                {{ $activity->user->fullname ?? $activity->reviewer->name }}
                                                - [{{ $activity->user_id ? __('You') : __('Reviewer') }}]
                                            </h6>
                                            <span class="product-response-info__date">
                                                {{ showDateTime($activity->created_at, 'd M Y ') }}
                                                @lang('at')
                                                {{ showDateTime($activity->created_at, 'H:ma') }}
                                            </span>
                                            <div class="product-response-list mt-1">
                                                <p>{{ $activity->message }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty

                            @endforelse --}}

                        </div>

                        @if ($activities->count() > 0)
                            <div class="d-flex align-items-center  mt-3">
                                <div class="me-2 activity-log">
                                    <x-author-avatar :author="auth()->user()" />
                                </div>
                                <div class="w-100">
                                    <p class="mb-2 fw-bold">@lang('Message to Reviewer')</p>
                                    <form action="{{ route('user.product.activities.reply', $product->id) }}" method="POST">
                                        @csrf
                                        <div class="input-group w-100">
                                            <input type="text" class="form--control  w-100" name="message" placeholder="@lang('You can reply to reviewer')" style="flex: 1">
                                            <button type="submit" class="btn btn--sm btn--base flex-shrink-1">@lang('Submit')</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @include('Template::partials.common_sidebar')
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.rejectBtn').on('click', function() {
                var modal = $('#rejectModal');
                modal.find('.modal-title').text($(this).data('title'));
                modal.find('form').attr('action', $(this).data('action'));
                modal.find('.reason-message').text($(this).data('reject-label') || "@lang('Reason for rejection')");
                modal.modal('show');
            });


            let page = 2;
            let isLoading = false;
            let hasMore = true;

            const container = document.getElementById('activity-container');
            const loader = document.getElementById('loader');
            const activityList = document.getElementById('activity-list');

            container.addEventListener('scroll', async () => {
                if (isLoading || !hasMore) return;

                if (container.scrollTop + container.clientHeight >= container.scrollHeight - 50) {
                    isLoading = true;
                    loader.classList.remove('d-none');

                    try {
                        const response = await fetch(
                            `{{ route('user.product.activities.ajax', $product->slug) }}?page=${page}`, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                        if (!response.ok) {
                            throw new Error('HTTP error ' + response.status);
                        }

                        const result = await response.json();
                        if (!result.hasMore || result.html.trim() === '') {
                            hasMore = false;
                        } else {
                            activityList.insertAdjacentHTML('beforeend', result.html);
                            page++;
                        }

                    } catch (err) {
                        console.error('Failed to load more activities:', err);
                        hasMore = false;
                    }

                    loader.classList.add('d-none');
                    isLoading = false;
                }
            });
        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .product-response-info__date {
            font-size: .75rem;
        }

        .activity-log .author-avatar {
            height: 60px;
            width: 60px;
            margin-top: 29px;
        }

        .product-response__content.activity {
            max-height: 600px;
            overflow-y: auto;
        }

        .product-response-info__thumb {
            border-radius: 50%;
            border: 1px solid #ddd;
        }
    </style>
@endpush
