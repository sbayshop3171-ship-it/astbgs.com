@pushOnce('style')
    <style>
        .wrapper-header,
        .product,
        .product-details {
            overflow-x: clip;
        }

        .header-top__right,
        .header-utility-actions,
        .header-nav-actions,
        .premium-cart-row,
        .catalog-cart-actions,
        .product-card__actions,
        .product-card__content,
        .product-details-top {
            min-width: 0;
        }

        .header-top__right {
            flex-wrap: wrap;
            justify-content: flex-end;
            row-gap: 12px;
        }

        .header-utility-actions,
        .top-menu-list {
            flex-wrap: wrap;
            justify-content: flex-end;
            row-gap: 8px;
        }

        .top-menu-list {
            margin-left: 0;
            gap: 8px 14px;
        }

        .top-menu-list__item {
            padding-right: 0;
        }

        .header-nav-actions {
            width: auto;
            max-width: 100%;
            margin-left: auto;
            flex: 0 0 auto;
            justify-content: flex-end;
        }

        .header-nav-actions__btn {
            max-width: 100%;
            flex: 0 1 auto;
            white-space: nowrap;
        }

        .language_switcher {
            max-width: min(100%, 170px);
        }

        .language_switcher__caption,
        .header-profile-info,
        .profile-info__button,
        .profile-info__content,
        .product-detail-main-col,
        .product-detail-sidebar-col,
        .common-sidebar,
        .common-sidebar__item,
        .common-sidebar__content,
        .product-details-top__inner,
        .product-card__rating {
            min-width: 0;
        }

        .language_switcher__caption .text,
        .profile-info__name {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .header-profile-info {
            max-width: min(100%, 220px);
            flex-shrink: 1;
        }

        .profile-info__button {
            max-width: 100%;
        }

        .header-cart-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            overflow: hidden;
            isolation: isolate;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.04);
            color: #fff;
            box-shadow: none;
            transition: background-color .2s ease, border-color .2s ease, transform .18s ease, box-shadow .2s ease;
        }

        .header-cart-link::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.82);
            opacity: 0;
            transform: translate(-50%, -50%) scale(.2);
            transition: transform .35s ease, opacity .35s ease;
            pointer-events: none;
            z-index: 0;
        }

        .header-cart-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.28);
            transform: translateY(-1px);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.14);
        }

        .header-cart-link:active {
            transform: translateY(0) scale(.97);
        }

        .header-cart-link:active::after,
        .header-cart-link:focus-visible::after {
            opacity: .14;
            transform: translate(-50%, -50%) scale(7);
        }

        .header-cart-link:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.16);
        }

        .header-cart-link__icon,
        .header-cart-link__badge {
            position: relative;
            z-index: 1;
        }

        .header-cart-link__icon {
            font-size: 1.25rem;
            line-height: 1;
        }

        .header-cart-link__badge {
            position: absolute;
            top: -6px;
            right: -6px;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: hsl(var(--base));
            color: #fff;
            font-size: .72rem;
            font-weight: 700;
            line-height: 1;
            border: 2px solid #19171c;
        }

        .header-cart-link.is-bumped {
            animation: cartBadgeBump .35s ease;
        }

        .product-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .product-card__thumb {
            position: relative;
            z-index: 1;
            max-height: none;
            aspect-ratio: 4 / 3;
            border-radius: 14px;
            overflow: hidden;
            border: 0;
            background: transparent;
        }

        .product-card__thumb::before {
            display: none;
        }

        .product-card__thumb .link {
            width: 100%;
            height: 100%;
            display: grid;
            place-items: center;
            padding: 0;
        }

        .product-card__thumb img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            object-position: center;
            transition: transform .22s ease, filter .22s ease;
        }

        .product-card:hover .product-card__thumb img {
            transform: scale(1.015);
            filter: saturate(1.02);
        }

        .product-card__content {
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
        }

        .product-card__content-inner {
            gap: 12px;
            margin-bottom: 14px;
        }

        .product-card__content>.flex-between {
            margin-top: auto;
            align-items: stretch !important;
        }

        .product-card__title .link {
            display: -webkit-box;
            width: 100%;
            max-width: 100%;
            overflow: hidden;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            text-wrap: pretty;
        }

        .product-card__author {
            display: grid;
            gap: 6px;
        }

        .product-card__author .link,
        .product-card__price {
            overflow-wrap: anywhere;
        }

        .product-card__actions {
            width: 100%;
            max-width: none;
        }

        .premium-cart-form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .premium-cart-form--card {
            width: 100%;
        }

        .premium-cart-card__option,
        .premium-cart-option-summary,
        .catalog-card-actions {
            width: 100%;
        }

        .premium-cart-select {
            min-height: 46px;
            border-radius: 12px;
            border: 1px solid #d9dee7;
            background: #fff;
            box-shadow: none;
        }

        .premium-cart-select:focus {
            border-color: hsl(var(--base) / .45);
            box-shadow: 0 0 0 3px hsl(var(--base) / .12);
        }

        .premium-cart-option-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 10px 12px;
            border: 1px solid #e6e9ef;
            border-radius: 12px;
            background: #f8fafc;
        }

        .premium-cart-option-summary__price,
        .premium-cart-option-summary__meta {
            font-size: .8rem;
            color: #334155;
        }

        .premium-cart-option-summary__price {
            font-weight: 700;
            color: #111827;
        }

        .premium-cart-row {
            display: grid;
            grid-template-columns: minmax(126px, 146px) minmax(0, 1fr);
            gap: 12px;
            align-items: stretch;
        }

        .premium-cart-quantity-group .premium-qty-selector {
            max-width: 100%;
        }

        .premium-qty-selector {
            width: 100%;
            max-width: 146px;
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            border: 1px solid #d9dee7;
            border-radius: 12px;
            background: #fff;
            overflow: hidden;
        }

        .premium-qty-selector__btn {
            width: 42px;
            height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: #f8fafc;
            color: #374151;
            transition: background-color .18s ease, color .18s ease, transform .18s ease;
        }

        .premium-qty-selector__btn:hover {
            background: #eef2f7;
            color: #111827;
        }

        .premium-qty-selector__btn:active {
            transform: scale(.94);
        }

        .premium-qty-selector__input {
            flex: 1 1 auto;
            width: 100%;
            min-width: 0;
            border: none;
            background: transparent;
            text-align: center;
            font-weight: 700;
            color: #111827;
            box-shadow: none;
            outline: none;
        }

        .premium-qty-selector__input::-webkit-outer-spin-button,
        .premium-qty-selector__input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .premium-qty-selector__input[type=number] {
            -moz-appearance: textfield;
        }

        .catalog-cart-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .catalog-cart-actions--single {
            grid-template-columns: 1fr;
        }

        .catalog-action-btn {
            position: relative;
            min-height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            overflow: hidden;
            isolation: isolate;
            padding: 10px 14px;
            border: 1px solid #d9dee7;
            border-radius: 12px;
            background: #fff;
            box-shadow: none;
            color: #1f2937;
            font-size: .95rem;
            font-weight: 600;
            line-height: 1.2;
            text-align: center;
            text-decoration: none;
            white-space: nowrap;
            transition: transform .18s ease, background-color .18s ease, border-color .18s ease, color .18s ease, box-shadow .18s ease;
        }

        .catalog-action-btn::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            opacity: 0;
            transform: translate(-50%, -50%) scale(.2);
            transition: transform .45s ease, opacity .35s ease;
            pointer-events: none;
            z-index: 0;
        }

        .catalog-action-btn>* {
            position: relative;
            z-index: 1;
        }

        .catalog-action-btn:hover {
            color: #111827;
            text-decoration: none;
            background: #f8fafc;
            border-color: #cfd6e0;
            transform: translateY(-1px);
            box-shadow: 0 10px 18px rgba(15, 23, 42, 0.08);
        }

        .catalog-action-btn:active {
            transform: translateY(0) scale(.985);
        }

        .catalog-action-btn:active::after,
        .catalog-action-btn:focus-visible::after {
            opacity: .12;
            transform: translate(-50%, -50%) scale(8);
        }

        .catalog-action-btn:focus-visible {
            outline: none;
            box-shadow: 0 0 0 3px hsl(var(--base) / .14);
        }

        .catalog-action-btn:disabled,
        .catalog-action-btn.disabled {
            opacity: .7;
            cursor: not-allowed;
            pointer-events: none;
        }

        .catalog-action-btn--primary {
            background: hsl(var(--base));
            border-color: hsl(var(--base));
            color: #fff;
        }

        .catalog-action-btn--primary::after {
            background: rgba(255, 255, 255, 0.9);
        }

        .catalog-action-btn--primary:hover {
            color: #fff;
            background: hsl(var(--base) / .92);
            border-color: hsl(var(--base) / .92);
            box-shadow: 0 12px 20px rgba(22, 163, 74, 0.18);
        }

        .catalog-action-btn--secondary {
            background: #fff;
            border-color: hsl(var(--base) / .3);
            color: hsl(var(--base));
        }

        .catalog-action-btn--secondary::after,
        .catalog-action-btn--light::after {
            background: hsl(var(--base));
        }

        .catalog-action-btn--secondary:hover {
            color: hsl(var(--base));
            background: hsl(var(--base) / .08);
            border-color: hsl(var(--base) / .4);
        }

        .catalog-action-btn--light {
            color: #4b5563;
            background: #fff;
        }

        .catalog-action-btn--light:hover {
            color: #334155;
            background: #f8fafc;
        }

        .catalog-action-btn--block {
            width: 100%;
        }

        .catalog-view-btn {
            min-height: 52px;
            font-size: .94rem;
        }

        .catalog-action-btn__spinner {
            display: none;
            width: 1rem;
            height: 1rem;
            border-width: .14em;
        }

        .catalog-action-btn.is-loading .catalog-action-btn__spinner {
            display: inline-block;
        }

        .catalog-action-btn.is-loading [data-cart-submit-icon] {
            display: none;
        }

        .catalog-action-btn.is-loading {
            opacity: .92;
        }

        .catalog-action-btn.is-success {
            border-color: #15803d;
            background: #eefbf3;
            color: #166534;
            animation: catalogButtonSuccessPulse .45s ease;
        }

        .catalog-action-btn--primary.is-success {
            background: #166534;
            border-color: #166534;
            color: #fff;
        }

        @media (min-width: 992px) {
            .header-navbar-collapse {
                display: flex !important;
                align-items: center;
                flex-wrap: wrap;
                gap: 14px 24px;
            }

            .header .nav-menu {
                flex: 1 1 auto;
                display: flex;
                flex-wrap: wrap;
                gap: 10px 24px;
                min-width: 0;
            }

            .header .nav-menu .nav-item {
                flex: 0 0 auto;
            }

            .header .nav-menu .nav-link {
                padding: 10px 0;
                white-space: nowrap;
            }
        }

        @media (min-width: 425px) {
            .list-view .product-card {
                display: flex;
                align-items: stretch;
                gap: 18px;
            }

            .list-view .product-card__thumb {
                flex: 0 0 clamp(180px, 31%, 280px);
                width: clamp(180px, 31%, 280px);
                max-width: none;
            }

            .list-view .product-card__content {
                width: auto;
                padding-left: 0;
                margin-top: 0;
                flex: 1 1 auto;
            }

            .list-view .product-card__content>.flex-between {
                flex-direction: column;
                gap: 14px;
            }
        }

        @media (max-width: 1199px) {
            .header-nav-actions {
                justify-content: flex-start;
            }
        }

        @media (max-width: 991px) {
            .header-navbar-collapse {
                gap: 16px;
                padding-top: 14px;
            }

            .header-nav-actions {
                justify-content: stretch;
            }

            .header-nav-actions__btn {
                flex: 1 1 calc(50% - 8px);
                justify-content: center;
            }
        }

        @media (max-width: 767px) {
            .product-card__content>.flex-between,
            .premium-cart-row,
            .catalog-cart-actions {
                grid-template-columns: 1fr;
            }

            .product-card__content>.flex-between {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .product-card__actions,
            .premium-qty-selector {
                max-width: 100%;
            }
        }

        @media (max-width: 575px) {
            .header-top__right,
            .header-utility-actions {
                width: 100%;
            }

            .header-utility-actions {
                justify-content: space-between;
            }

            .language_switcher {
                max-width: calc(100% - 72px);
            }

            .header-profile-info {
                max-width: calc(100% - 58px);
            }

            .top-menu-list {
                width: 100%;
                justify-content: flex-start;
            }

            .header-cart-link {
                width: 40px;
                height: 40px;
            }

            .header-cart-link__badge {
                top: -5px;
                right: -5px;
            }
        }

        @media (max-width: 424px) {
            .header-nav-actions__btn,
            .header-profile-info,
            .language_switcher {
                width: 100%;
                max-width: 100%;
            }

            .header-utility-actions,
            .top-menu-list {
                justify-content: stretch;
            }

            .product-card__thumb {
                aspect-ratio: 4 / 3;
            }

            .product-card__thumb .link {
                padding: 10px;
            }

            .catalog-action-btn {
                white-space: normal;
            }
        }

        @keyframes cartBadgeBump {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.08);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes catalogButtonSuccessPulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.03);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
@endPushOnce

@pushOnce('script')
    <script>
        (function($) {
            "use strict";

            const csrfToken = @json(csrf_token());

            function toast(status, message) {
                if (typeof notify === 'function') {
                    notify(status, message);
                }
            }

            function clampQuantity(value) {
                const parsed = parseInt(value, 10);

                if (Number.isNaN(parsed) || parsed < 1) {
                    return 1;
                }

                return Math.min(parsed, 99);
            }

            function resolveForm($element) {
                const formAttribute = $element.attr('form');

                if (formAttribute) {
                    return $('#' + formAttribute);
                }

                return $element.closest('form[data-ajax-cart-form]');
            }

            function getFormButtons($form) {
                let $buttons = $form.find('[data-cart-submit]');
                const formId = $form.attr('id');

                if (formId) {
                    $buttons = $buttons.add($(`[data-cart-submit][form="${formId}"]`));
                }

                return $buttons;
            }

            function setButtonLabel($button, label) {
                $button.find('[data-cart-submit-text]').text(label);
            }

            function setButtonIdle($button) {
                $button.removeClass('is-loading is-success');
                $button.prop('disabled', false);
                setButtonLabel($button, $button.attr('data-idle-label') || 'Add to Cart');
            }

            function setFormIdle($form) {
                getFormButtons($form).each(function() {
                    setButtonIdle($(this));
                });
            }

            function setFormLoading($form, $activeButton) {
                const $buttons = getFormButtons($form);

                $buttons.each(function() {
                    $(this).removeClass('is-loading is-success').prop('disabled', true);
                });

                $activeButton.addClass('is-loading');
                setButtonLabel($activeButton, $activeButton.attr('data-loading-label') || 'Adding...');
            }

            function setFormSuccess($form, $activeButton) {
                const $buttons = getFormButtons($form);

                $buttons.each(function() {
                    $(this).removeClass('is-loading is-success').prop('disabled', true);
                    setButtonLabel($(this), $(this).attr('data-idle-label') || 'Add to Cart');
                });

                $activeButton.addClass('is-success');
                setButtonLabel($activeButton, $activeButton.attr('data-success-label') || 'Added!');
            }

            function updateCartCounter(count) {
                $('[data-cart-count]').text(count);
                $('.header-cart-link').addClass('is-bumped');

                window.setTimeout(() => {
                    $('.header-cart-link').removeClass('is-bumped');
                }, 350);
            }

            function refreshOptionPresentation($form) {
                const $optionSelect = $form.find('[data-cart-option-select]');

                if (!$optionSelect.length) {
                    return;
                }

                const $selected = $optionSelect.find('option:selected');
                const hasValue = !!$selected.val();
                const pricePlaceholder = $form.data('cartPricePlaceholder') || '';
                const emptyLabel = $form.data('cartPriceEmptyLabel') || pricePlaceholder;
                const price = hasValue ? ($selected.data('price') || pricePlaceholder) : emptyLabel;
                const min = $selected.data('min');
                const max = $selected.data('max');
                const note = $selected.data('note');
                const hasRange = hasValue && ((min !== undefined && min !== '') || (max !== undefined && max !== ''));
                const minText = min !== undefined && min !== '' ? min : '0';
                const maxText = max !== undefined && max !== '' ? max : 'Any';

                $form.attr('data-cart-option-required', hasValue ? 'false' : 'true');

                const $optionMetaBox = $form.find('.option-meta-box');
                const $selectedPrice = $form.find('.selected-price');
                const $selectedPricingType = $form.find('.selected-pricing-type');
                const $optionRangeText = $form.find('.option-range-text');
                const $selectedRange = $form.find('.selected-range');
                const $optionNoteText = $form.find('.option-note-text');
                const $selectedNote = $form.find('.selected-note');
                const $requestedAmountGroup = $form.find('.requested-amount-group');
                const $requestedAmountInput = $form.find('.requested-amount-input');
                const $priceHeading = $form.find('.catalog-price-heading');

                $optionMetaBox.toggleClass('d-none', !hasValue);
                $selectedPrice.text(hasValue ? price : pricePlaceholder);

                if ($selectedPricingType.length) {
                    $selectedPricingType.text(hasRange ? @json(__('Min-Max Range')) : @json(__('Fixed Price')));
                }

                $optionRangeText.toggleClass('d-none', !hasRange);

                if (hasRange) {
                    $selectedRange.text(`${minText} - ${maxText}`);
                } else {
                    $selectedRange.text('');
                }

                $optionNoteText.toggleClass('d-none', !hasValue || !note);
                $selectedNote.text(note || '');

                if ($requestedAmountGroup.length) {
                    $requestedAmountGroup.toggleClass('d-none', !hasRange);

                    if (hasRange) {
                        $requestedAmountInput.attr('placeholder', `${minText} - ${maxText}`);

                        if (min !== undefined && min !== '') {
                            $requestedAmountInput.attr('min', min);
                        } else {
                            $requestedAmountInput.removeAttr('min');
                        }

                        if (max !== undefined && max !== '') {
                            $requestedAmountInput.attr('max', max);
                        } else {
                            $requestedAmountInput.removeAttr('max');
                        }
                    } else {
                        $requestedAmountInput.val('').removeAttr('min').removeAttr('max');
                    }
                }

                if ($priceHeading.length) {
                    $priceHeading.text(price);
                }
            }

            function extractErrorMessage(xhr) {
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        return Object.values(xhr.responseJSON.errors).flat()[0];
                    }

                    if (xhr.responseJSON.message) {
                        return xhr.responseJSON.message;
                    }
                }

                return 'Something went wrong while updating your cart.';
            }

            function getIntentContext($form, nativeSubmitter) {
                let $button = $();

                if (nativeSubmitter) {
                    $button = $(nativeSubmitter);
                }

                if (!$button.length || !$button.is('[data-cart-submit]')) {
                    const storedButton = $form.data('activeSubmitButton');

                    if (storedButton) {
                        $button = $(storedButton);
                    }
                }

                if (!$button.length) {
                    $button = getFormButtons($form).filter('[data-cart-intent="cart"]').first();
                }

                if (!$button.length) {
                    $button = getFormButtons($form).first();
                }

                return {
                    button: $button,
                    intent: String($button.data('cartIntent') || 'cart'),
                };
            }

            function setRedirectValue($form, intent) {
                $form.find('input[name="redirect_to"]').val(intent === 'checkout' ? 'checkout' : 'cart');
            }

            function focusField($field) {
                if (!$field.length) {
                    return;
                }

                $field.trigger('focus');

                if ($field[0] && typeof $field[0].scrollIntoView === 'function') {
                    $field[0].scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                    });
                }
            }

            function validateCartForm($form) {
                const $optionSelect = $form.find('[data-cart-option-select]');
                const requiresOption = $form.attr('data-cart-option-required') === 'true';
                const $requestedAmountGroup = $form.find('.requested-amount-group');
                const $requestedAmountInput = $requestedAmountGroup.find('.requested-amount-input');

                if (requiresOption && $optionSelect.length && !$optionSelect.val()) {
                    return {
                        message: 'Please select an option before continuing.',
                        field: $optionSelect,
                    };
                }

                if ($requestedAmountGroup.length && !$requestedAmountGroup.hasClass('d-none') && !$requestedAmountInput.val()) {
                    return {
                        message: 'Please enter a requested amount within the allowed range.',
                        field: $requestedAmountInput,
                    };
                }

                return null;
            }

            function submitCartForm($form, intent, $activeButton) {
                setRedirectValue($form, intent);
                setFormLoading($form, $activeButton);

                $.ajax({
                    url: $form.attr('action'),
                    type: $form.attr('method') || 'POST',
                    data: new FormData($form[0]),
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    success: function(response) {
                        updateCartCounter(response.cart_count ?? 0);
                        setFormSuccess($form, $activeButton);

                        if (response.message) {
                            toast('success', response.message);
                        }

                        const redirectUrl = intent === 'checkout' ? (response.redirect_url || response.checkout_url) : null;

                        if (redirectUrl) {
                            window.setTimeout(() => {
                                window.location.href = redirectUrl;
                            }, 180);
                            return;
                        }

                        window.setTimeout(() => {
                            setFormIdle($form);
                        }, 900);
                    },
                    error: function(xhr) {
                        setFormIdle($form);
                        refreshOptionPresentation($form);
                        toast('error', extractErrorMessage(xhr));
                    }
                });
            }

            $(document).on('click', '[data-cart-submit]', function() {
                const $form = resolveForm($(this));

                if (!$form.length) {
                    return;
                }

                $form.data('activeSubmitButton', this);
                setRedirectValue($form, String($(this).data('cartIntent') || 'cart'));
            });

            $(document).on('change', '[data-cart-option-select]', function() {
                const $form = resolveForm($(this));

                if ($form.length) {
                    refreshOptionPresentation($form);
                }
            });

            $(document).on('click', '[data-quantity-control]', function() {
                const $wrapper = $(this).closest('[data-cart-qty-wrapper]');
                const $input = $wrapper.find('[data-cart-quantity-input]').first();

                if (!$input.length || $input.is(':disabled')) {
                    return;
                }

                const current = clampQuantity($input.val());
                const action = $(this).data('quantityControl');
                const nextValue = action === 'increase' ? Math.min(current + 1, 99) : Math.max(current - 1, 1);

                $input.val(nextValue).trigger('change');
            });

            $(document).on('change', '[data-cart-quantity-input]', function() {
                $(this).val(clampQuantity($(this).val()));
            });

            $(document).on('submit', 'form[data-ajax-cart-form]', function(event) {
                event.preventDefault();

                const $form = $(this);
                const nativeSubmitter = event.originalEvent && event.originalEvent.submitter ? event.originalEvent.submitter : null;
                const context = getIntentContext($form, nativeSubmitter);
                const validation = validateCartForm($form);

                if (validation) {
                    toast('warning', validation.message);
                    refreshOptionPresentation($form);
                    focusField(validation.field);
                    return;
                }

                submitCartForm($form, context.intent, context.button);
            });

            window.refreshPremiumCartForms = function(container) {
                const $scope = container ? $(container) : $(document);

                $scope.find('form[data-ajax-cart-form]').each(function() {
                    const $form = $(this);
                    setFormIdle($form);
                    refreshOptionPresentation($form);
                });
            };

            $(function() {
                window.refreshPremiumCartForms();
            });
        })(jQuery);
    </script>
@endPushOnce
