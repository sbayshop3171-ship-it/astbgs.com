@pushOnce('style')
    <style>
        .header-cart-chip {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(10, 37, 64, 0.96), rgba(24, 113, 103, 0.94));
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 16px 32px rgba(15, 23, 42, 0.16);
            color: #fff;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .header-cart-chip:hover {
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.2);
        }

        .header-cart-chip__icon {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
            font-size: 1.2rem;
        }

        .header-cart-chip__content {
            display: flex;
            flex-direction: column;
            line-height: 1.05;
        }

        .header-cart-chip__label {
            font-size: .72rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.72);
        }

        .header-cart-chip__count {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
        }

        .header-cart-chip.is-bumped {
            animation: cartChipBump .45s ease;
        }

        .product-card__actions {
            width: min(100%, 320px);
        }

        .premium-cart-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .premium-cart-form--card {
            width: 100%;
        }

        .premium-cart-card__option,
        .premium-cart-option-summary {
            width: 100%;
        }

        .premium-cart-select {
            min-height: 44px;
            border-radius: 14px;
            border: 1px solid rgba(15, 23, 42, 0.12);
            box-shadow: none;
            background-color: #fff;
        }

        .premium-cart-option-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(20, 184, 166, 0.08), rgba(15, 23, 42, 0.04));
            border: 1px solid rgba(20, 184, 166, 0.15);
        }

        .premium-cart-option-summary__price,
        .premium-cart-option-summary__meta {
            font-size: .8rem;
            color: #0f172a;
        }

        .premium-cart-option-summary__price {
            font-weight: 700;
        }

        .premium-cart-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .premium-cart-quantity-group .premium-qty-selector {
            width: 100%;
        }

        .premium-qty-selector {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            min-width: 126px;
            padding: 6px;
            border-radius: 18px;
            border: 1px solid rgba(15, 23, 42, 0.1);
            background: #fff;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
        }

        .premium-qty-selector__btn {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 12px;
            background: rgba(15, 23, 42, 0.06);
            color: #0f172a;
            transition: transform .2s ease, background-color .2s ease, color .2s ease;
        }

        .premium-qty-selector__btn:hover {
            background: hsl(var(--base) / .14);
            color: hsl(var(--base));
            transform: scale(1.03);
        }

        .premium-qty-selector__input {
            width: 52px;
            border: none;
            background: transparent;
            text-align: center;
            font-weight: 700;
            color: #0f172a;
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

        .premium-cart-submit {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 52px;
            padding: 0 18px;
            border: none;
            border-radius: 18px;
            background: linear-gradient(135deg, #0f766e 0%, #14b8a6 55%, #0ea5e9 100%);
            color: #fff;
            font-weight: 700;
            box-shadow: 0 18px 40px rgba(20, 184, 166, 0.28);
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
            overflow: hidden;
            white-space: nowrap;
        }

        .premium-cart-submit:hover {
            color: #fff;
            transform: translateY(-1px);
            filter: saturate(1.05);
            box-shadow: 0 20px 42px rgba(20, 184, 166, 0.32);
        }

        .premium-cart-submit--sidebar {
            width: 100%;
            min-height: 56px;
        }

        .premium-cart-submit--sticky {
            flex: 1 1 auto;
        }

        .premium-cart-form--card .premium-cart-submit {
            flex: 1 1 auto;
        }

        .premium-cart-submit__state {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .premium-cart-submit:not(.is-loading):not(.is-success) .premium-cart-submit__state--default,
        .premium-cart-submit.is-loading .premium-cart-submit__state--loading,
        .premium-cart-submit.is-success .premium-cart-submit__state--success {
            display: inline-flex;
        }

        .premium-cart-submit.is-loading {
            pointer-events: none;
            box-shadow: 0 12px 26px rgba(15, 23, 42, 0.18);
        }

        .premium-cart-submit.is-success {
            background: linear-gradient(135deg, #15803d 0%, #22c55e 100%);
            box-shadow: 0 16px 34px rgba(34, 197, 94, 0.28);
        }

        .premium-cart-submit.is-attention {
            animation: cartGlow 1.6s ease-in-out infinite;
        }

        .catalog-sticky-cart {
            position: fixed;
            left: 12px;
            right: 12px;
            bottom: 12px;
            z-index: 1050;
            display: none;
            align-items: center;
            gap: 12px;
            padding: 14px;
            border-radius: 24px;
            background: rgba(15, 23, 42, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.35);
            backdrop-filter: blur(18px);
        }

        .catalog-sticky-cart__meta {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .catalog-sticky-cart__label {
            color: rgba(255, 255, 255, 0.62);
            font-size: .72rem;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .catalog-sticky-cart__price {
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
        }

        .catalog-sticky-cart__actions {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        body.has-sticky-cart-ui {
            padding-bottom: 112px;
        }

        @media (max-width: 1199px) {
            .premium-cart-row {
                flex-direction: column;
                align-items: stretch;
            }

            .product-card__actions {
                width: 100%;
            }
        }

        @media (max-width: 991px) {
            .catalog-sticky-cart {
                display: flex;
            }
        }

        @media (max-width: 575px) {
            .header-cart-chip {
                padding: 8px 12px;
            }

            .header-cart-chip__icon {
                width: 34px;
                height: 34px;
            }

            .premium-cart-submit,
            .premium-cart-select {
                min-height: 48px;
            }

            .catalog-sticky-cart {
                left: 10px;
                right: 10px;
                bottom: 10px;
                flex-direction: column;
                align-items: stretch;
            }

            .catalog-sticky-cart__actions {
                margin-left: 0;
                width: 100%;
            }
        }

        @keyframes cartChipBump {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }

        @keyframes cartGlow {
            0%,
            100% {
                box-shadow: 0 18px 40px rgba(251, 191, 36, 0.18);
            }

            50% {
                box-shadow: 0 22px 44px rgba(251, 191, 36, 0.34);
            }
        }
    </style>
@endPushOnce

@pushOnce('script')
    <script>
        (function($) {
            "use strict";

            const csrfToken = @json(csrf_token());
            const syncTimers = new Map();

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

            function normalizeComparableAmount(value) {
                if (value === undefined || value === null || value === '') {
                    return '';
                }

                const parsed = parseFloat(value);

                return Number.isNaN(parsed) ? String(value) : parsed.toFixed(2);
            }

            function getCartKey($form) {
                return $form.attr('data-cart-key') || '';
            }

            function setCartKey($form, cartKey) {
                $form.attr('data-cart-key', cartKey || '');
                $form.data('cartKey', cartKey || '');
            }

            function setCartItemUrl($form, url) {
                $form.attr('data-cart-item-url', url || '');
                $form.data('cartItemUrl', url || '');
            }

            function setCartSelectionState($form, responseItem) {
                $form.attr('data-cart-option-id', responseItem ? (responseItem.product_option_id || '') : '');
                $form.attr('data-cart-requested-amount', responseItem ? (responseItem.requested_amount || '') : '');
            }

            function hasMatchingCartSelection($form) {
                if (!getCartKey($form)) {
                    return false;
                }

                const $optionSelect = $form.find('[data-cart-option-select]');
                if (!$optionSelect.length) {
                    return true;
                }

                const activeOptionId = String($form.attr('data-cart-option-id') || '');
                const currentOptionId = String($optionSelect.val() || '');

                if (activeOptionId !== currentOptionId) {
                    return false;
                }

                const currentRequestedAmount = normalizeComparableAmount($form.find('.requested-amount-input').val());
                const activeRequestedAmount = normalizeComparableAmount($form.attr('data-cart-requested-amount'));

                return currentRequestedAmount === activeRequestedAmount;
            }

            function getIdleLabel($form) {
                return hasMatchingCartSelection($form) ? ($form.data('cartUpdatedLabel') || 'Update Cart') : ($form.data('cartDefaultLabel') || 'Add to Cart');
            }

            function resolveForm($element) {
                const formAttribute = $element.attr('form');
                if (formAttribute) {
                    return $('#' + formAttribute);
                }

                const $form = $element.closest('form[data-ajax-cart-form]');
                if ($form.length) {
                    return $form;
                }

                const targetForm = $element.closest('[data-sticky-cart]').data('targetForm');
                if (targetForm) {
                    return $('#' + targetForm);
                }

                return $();
            }

            function getFormButtons($form) {
                let $buttons = $form.find('[data-cart-submit]');
                const formId = $form.attr('id');

                if (formId) {
                    $buttons = $buttons.add($(`[data-cart-submit][form="${formId}"]`));
                }

                return $buttons;
            }

            function setLoadingLabel($form, label) {
                getFormButtons($form).find('[data-cart-submit-loading]').text(label);
            }

            function setDefaultLabel($form, label) {
                getFormButtons($form).find('[data-cart-submit-text]').text(label);
            }

            function setSubmitState($form, state) {
                const $buttons = getFormButtons($form);

                $buttons.removeClass('is-loading is-success');

                if (state === 'loading') {
                    $buttons.addClass('is-loading');
                } else if (state === 'success') {
                    $buttons.addClass('is-success');
                }
            }

            function updateCartCounter(count) {
                $('[data-cart-count]').text(count);
                $('.header-cart-chip').addClass('is-bumped');

                setTimeout(() => {
                    $('.header-cart-chip').removeClass('is-bumped');
                }, 420);
            }

            function getGroupName($input) {
                return $input.attr('data-cart-quantity-group');
            }

            function syncQuantityGroup(groupName, value) {
                if (!groupName) {
                    return;
                }

                $(`[data-cart-quantity-group="${groupName}"]`).each(function() {
                    $(this).val(value);
                });
            }

            function refreshOptionPresentation($form) {
                const $optionSelect = $form.find('[data-cart-option-select]');
                if (!$optionSelect.length) {
                    return;
                }

                const $selected = $optionSelect.find('option:selected');
                const hasValue = !!$selected.val();
                const pricePlaceholder = $form.data('cartPricePlaceholder') || '';
                const price = hasValue ? ($selected.data('price') || pricePlaceholder) : pricePlaceholder;
                const min = $selected.data('min');
                const max = $selected.data('max');
                const note = $selected.data('note');
                const hasRange = hasValue && ((min !== undefined && min !== '') || (max !== undefined && max !== ''));
                const minText = min !== undefined && min !== '' ? min : '0';
                const maxText = max !== undefined && max !== '' ? max : 'Any';
                const stickyTarget = $form.data('stickyPriceTarget');

                $form.attr('data-cart-option-required', hasValue ? 'false' : 'true');
                getFormButtons($form).toggleClass('is-attention', !hasValue);

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
                $selectedPrice.text(price);

                if ($selectedPricingType.length) {
                    $selectedPricingType.text(hasRange ? @json(__('Min-Max Range')) : @json(__('Fixed Price')));
                }

                $optionRangeText.toggleClass('d-none', !hasRange);
                $selectedRange.text(`${minText} - ${maxText}`);

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
                        $requestedAmountInput.removeAttr('min').removeAttr('max');
                    }
                }

                if ($priceHeading.length) {
                    $priceHeading.text(price);
                }

                if (stickyTarget) {
                    $(stickyTarget).text(price);
                }

                setDefaultLabel($form, getIdleLabel($form));
            }

            function resetFormState($form, response) {
                if (response.item) {
                    setCartKey($form, response.item.cart_key);
                    setCartItemUrl($form, response.item.item_url);
                    setCartSelectionState($form, response.item);

                    const quantityInput = $form.find('[data-cart-quantity-input]').first();
                    const groupName = getGroupName(quantityInput);
                    syncQuantityGroup(groupName, response.item.quantity);
                    setDefaultLabel($form, getIdleLabel($form));
                } else {
                    setCartKey($form, '');
                    setCartItemUrl($form, '');
                    setCartSelectionState($form, null);

                    const quantityInput = $form.find('[data-cart-quantity-input]').first();
                    const groupName = getGroupName(quantityInput);
                    syncQuantityGroup(groupName, 1);
                    setDefaultLabel($form, $form.data('cartDefaultLabel') || 'Add to Cart');
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

            function finalizeForm($form) {
                window.setTimeout(() => {
                    setSubmitState($form, 'idle');
                    setDefaultLabel($form, getIdleLabel($form));
                }, 1200);
            }

            function renderCartResponse($form, response, showToast = true) {
                updateCartCounter(response.cart_count ?? 0);
                resetFormState($form, response);
                refreshOptionPresentation($form);
                setSubmitState($form, 'success');

                if (showToast && response.message) {
                    toast(response.item ? 'success' : 'info', response.message);
                }

                finalizeForm($form);
            }

            function sendQuantitySync($form, quantity, silent = true) {
                const itemUrl = $form.attr('data-cart-item-url');
                if (!itemUrl) {
                    return;
                }

                setLoadingLabel($form, quantity === 0 ? @json(__('Removing...')) : ($form.data('cartLabelUpdating') || 'Updating...'));
                setSubmitState($form, 'loading');

                $.ajax({
                    url: itemUrl,
                    type: 'POST',
                    data: (() => {
                        const formData = new FormData($form[0]);
                        formData.set('quantity', quantity);
                        return formData;
                    })(),
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    success: function(response) {
                        renderCartResponse($form, response, !silent || !response.item);
                    },
                    error: function(xhr) {
                        setSubmitState($form, 'idle');
                        setDefaultLabel($form, getIdleLabel($form));
                        toast('error', extractErrorMessage(xhr));
                    }
                });
            }

            function scheduleQuantitySync($form, quantity) {
                const syncKey = $form.attr('id') || getCartKey($form);
                window.clearTimeout(syncTimers.get(syncKey));

                syncTimers.set(syncKey, window.setTimeout(() => {
                    sendQuantitySync($form, quantity, true);
                }, 320));
            }

            $(document).on('change', '[data-cart-option-select]', function() {
                refreshOptionPresentation(resolveForm($(this)));
            });

            $(document).on('input change', '.requested-amount-input', function() {
                const $form = resolveForm($(this));
                if ($form.length) {
                    setDefaultLabel($form, getIdleLabel($form));
                }
            });

            $(document).on('click', '[data-quantity-control]', function() {
                const $form = resolveForm($(this));
                if (!$form.length) {
                    return;
                }

                const $wrapper = $(this).closest('[data-cart-qty-wrapper]');
                const $input = $wrapper.find('[data-cart-quantity-input]').first();
                const current = clampQuantity($input.val());
                const action = $(this).data('quantityControl');

                if (action === 'decrease' && current <= 1) {
                    if (hasMatchingCartSelection($form)) {
                        sendQuantitySync($form, 0, false);
                    }

                    return;
                }

                const nextValue = action === 'increase' ? Math.min(current + 1, 99) : Math.max(current - 1, 1);
                syncQuantityGroup(getGroupName($input), nextValue);

                if (hasMatchingCartSelection($form)) {
                    scheduleQuantitySync($form, nextValue);
                }
            });

            $(document).on('change', '[data-cart-quantity-input]', function() {
                const $input = $(this);
                const $form = resolveForm($input);
                if (!$form.length) {
                    return;
                }

                const normalizedValue = clampQuantity($input.val());
                syncQuantityGroup(getGroupName($input), normalizedValue);

                if (hasMatchingCartSelection($form)) {
                    scheduleQuantitySync($form, normalizedValue);
                }
            });

            $(document).on('submit', 'form[data-ajax-cart-form]', function(event) {
                event.preventDefault();

                const $form = $(this);
                const $optionSelect = $form.find('[data-cart-option-select]');
                const requiresOption = $form.attr('data-cart-option-required') === 'true';
                const $requestedAmountGroup = $form.find('.requested-amount-group');
                const $requestedAmountInput = $requestedAmountGroup.find('.requested-amount-input');

                if (requiresOption && $optionSelect.length && !$optionSelect.val()) {
                    toast('warning', 'Please select an option before adding this item to your cart.');
                    refreshOptionPresentation($form);
                    $optionSelect.trigger('focus');
                    if ($optionSelect[0] && typeof $optionSelect[0].scrollIntoView === 'function') {
                        $optionSelect[0].scrollIntoView({
                            behavior: 'smooth',
                            block: 'center',
                        });
                    }
                    return;
                }

                if ($requestedAmountGroup.length && !$requestedAmountGroup.hasClass('d-none') && !$requestedAmountInput.val()) {
                    toast('warning', 'Please enter a requested amount within the allowed range.');
                    $requestedAmountInput.trigger('focus');
                    return;
                }

                if (hasMatchingCartSelection($form)) {
                    sendQuantitySync($form, clampQuantity($form.find('[data-cart-quantity-input]').first().val()), false);
                    return;
                }

                setLoadingLabel($form, $form.data('cartLabelPending') || 'Adding...');
                setSubmitState($form, 'loading');

                $.ajax({
                    url: $form.attr('action'),
                    type: $form.attr('method') || 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    success: function(response) {
                        renderCartResponse($form, response, true);
                    },
                    error: function(xhr) {
                        setSubmitState($form, 'idle');
                        setDefaultLabel($form, getIdleLabel($form));
                        refreshOptionPresentation($form);
                        toast('error', extractErrorMessage(xhr));
                    }
                });
            });

            window.refreshPremiumCartForms = function(container) {
                const $scope = container ? $(container) : $(document);

                $scope.find('form[data-ajax-cart-form]').each(function() {
                    const $form = $(this);
                    setDefaultLabel($form, getIdleLabel($form));
                    refreshOptionPresentation($form);
                });
            };

            $(function() {
                window.refreshPremiumCartForms();

                if ($('[data-sticky-cart]').length) {
                    $('body').addClass('has-sticky-cart-ui');
                }
            });
        })(jQuery);
    </script>
@endPushOnce
