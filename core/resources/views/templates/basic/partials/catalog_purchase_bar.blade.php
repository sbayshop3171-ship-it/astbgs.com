@php
    $activeOptions = $product->relationLoaded('activeOptions') ? $product->activeOptions : $product->activeOptions()->get();
    $hasOptions = $activeOptions->isNotEmpty();
    $hasPurchased = auth()->check() ? auth()->user()->hasPurchasedProduct($product->id) : false;
    $availabilityClasses = [
        \App\Constants\Status::PRODUCT_AVAILABILITY_AVAILABLE => 'success',
        \App\Constants\Status::PRODUCT_AVAILABILITY_LIMITED => 'warning',
        \App\Constants\Status::PRODUCT_AVAILABILITY_UNAVAILABLE => 'danger',
    ];
    $availabilityClass = $availabilityClasses[$product->availability_status] ?? 'secondary';
@endphp

<div class="common-sidebar__item">
    <div class="common-sidebar__content">
        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
            <div>
                <h4 class="mb-1">{{ $product->catalogPriceLabel }}</h4>
                <p class="mb-0 text-muted small">@lang('Secure cart and checkout flow')</p>
            </div>
            <span class="badge badge--{{ $availabilityClass }}">{{ __(ucfirst($product->availability_status)) }}</span>
        </div>

        @if ($product->product_type === \App\Constants\Status::PRODUCT_TYPE_DOWNLOADABLE)
            <div class="alert alert--info mb-3 py-2 px-3">
                <small>@lang('Downloads unlock after successful payment and order completion.')</small>
            </div>
        @endif

        @if ($hasPurchased)
            <div class="alert alert--success mb-3 py-2 px-3">
                <small>@lang('You already purchased this item. Your latest files are available from My Orders.')</small>
            </div>
        @endif

        <form action="{{ route('cart.add', $product->slug) }}" method="POST" id="catalog-purchase-form-{{ $product->id }}">
            @csrf
            <input type="hidden" name="quantity" value="1">

            @if ($hasOptions)
                <div class="form-group">
                    <label class="form-label">@lang('Choose an option')</label>
                    <select name="product_option_id" class="form-control catalog-option-select" required>
                        <option value="">@lang('Select One')</option>
                        @foreach ($activeOptions as $option)
                            <option value="{{ $option->id }}" data-price="{{ showAmount($option->price) }}"
                                data-note="{{ e($option->availability_note ?? '') }}"
                                data-min="{{ $option->min_amount }}"
                                data-max="{{ $option->max_amount }}">
                                {{ __($option->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="border rounded p-3 mb-3 d-none option-meta-box">
                    <div class="small mb-2"><strong>@lang('Price:')</strong> <span class="selected-price">{{ $product->catalogPriceLabel }}</span></div>
                    <div class="small mb-2 d-none option-range-text"><strong>@lang('Allowed Range:')</strong> <span class="selected-range"></span></div>
                    <div class="small text-muted d-none option-note-text"><strong>@lang('Availability Note:')</strong> <span class="selected-note"></span></div>
                </div>
            @endif

            @if ($product->product_type === \App\Constants\Status::PRODUCT_TYPE_OPTION_REQUEST)
                <div class="form-group requested-amount-group d-none">
                    <label class="form-label">@lang('Requested Amount')</label>
                    <input type="number" step="any" min="0" name="requested_amount" class="form-control requested-amount-input"
                        value="{{ old('requested_amount') }}" placeholder="@lang('Enter amount within the allowed range')">
                </div>

                <div class="form-group">
                    <label class="form-label">@lang('Request Note')</label>
                    <textarea name="request_note" class="form-control" rows="4" placeholder="@lang('Add any information the admin should review')">{{ old('request_note') }}</textarea>
                </div>
            @endif

            <button type="submit" class="btn btn--base w-100">
                @lang($hasOptions ? 'Add to Cart' : $product->catalogActionLabel)
            </button>
        </form>

        <div class="common-sidebar__button mt-3">
            <a href="{{ route('cart.index') }}" class="btn btn-outline--base w-100">@lang('View Cart')</a>
        </div>

        @auth
            <div class="common-sidebar__button mt-2">
                <a href="{{ route('user.orders.index') }}" class="btn btn-outline--light w-100">@lang('My Orders')</a>
            </div>
        @else
            <p class="small text-muted mt-3 mb-0">@lang('You can add items to cart now and sign in at checkout.')</p>
        @endauth
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";

            const form = $('#catalog-purchase-form-{{ $product->id }}');
            if (!form.length) {
                return;
            }

            const optionSelect = form.find('.catalog-option-select');
            const optionMetaBox = form.find('.option-meta-box');
            const selectedPrice = form.find('.selected-price');
            const optionRangeText = form.find('.option-range-text');
            const selectedRange = form.find('.selected-range');
            const optionNoteText = form.find('.option-note-text');
            const selectedNote = form.find('.selected-note');
            const requestedAmountGroup = form.find('.requested-amount-group');
            const requestedAmountInput = form.find('.requested-amount-input');

            function toggleOptionDetails() {
                if (!optionSelect.length) {
                    return;
                }

                const option = optionSelect.find('option:selected');
                const hasValue = !!option.val();
                const price = option.data('price') || @json($product->catalogPriceLabel);
                const min = option.data('min');
                const max = option.data('max');
                const note = option.data('note');
                const hasRange = (min !== undefined && min !== '') || (max !== undefined && max !== '');

                optionMetaBox.toggleClass('d-none', !hasValue);
                selectedPrice.text(price);

                optionRangeText.toggleClass('d-none', !hasValue || !hasRange);
                if (hasRange) {
                    const minText = min !== undefined && min !== '' ? min : '0';
                    const maxText = max !== undefined && max !== '' ? max : 'Any';
                    selectedRange.text(`${minText} - ${maxText}`);
                }

                optionNoteText.toggleClass('d-none', !hasValue || !note);
                if (note) {
                    selectedNote.text(note);
                }

                @if ($product->product_type === \App\Constants\Status::PRODUCT_TYPE_OPTION_REQUEST)
                    requestedAmountGroup.toggleClass('d-none', !hasValue || !hasRange);
                    if (hasRange) {
                        if (min !== undefined && min !== '') {
                            requestedAmountInput.attr('min', min);
                        } else {
                            requestedAmountInput.removeAttr('min');
                        }

                        if (max !== undefined && max !== '') {
                            requestedAmountInput.attr('max', max);
                        } else {
                            requestedAmountInput.removeAttr('max');
                        }
                    } else {
                        requestedAmountInput.val('').removeAttr('min').removeAttr('max');
                    }
                @endif
            }

            optionSelect.on('change', toggleOptionDetails);
            toggleOptionDetails();
        })(jQuery);
    </script>
@endpush
