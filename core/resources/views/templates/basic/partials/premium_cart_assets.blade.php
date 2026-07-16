@pushOnce('style')
    <style>
        .header-cart-link {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.04);
            color: #fff;
            transition: background-color .2s ease, border-color .2s ease, transform .2s ease;
        }

        .header-cart-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.28);
            transform: translateY(-1px);
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

        .product-card__actions {
            width: min(100%, 320px);
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
            min-height: 44px;
            border-radius: 10px;
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
            border-radius: 10px;
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
            grid-template-columns: minmax(120px, 146px) minmax(0, 1fr);
            gap: 10px;
            align-items: stretch;
        }

        .premium-cart-quantity-group .premium-qty-selector {
            max-width: 100%;
        }

        .premium-qty-selector {
            width: 100%;
            max-width: 146px;
            min-height: 46px;
            display: inline-flex;
            align-items: center;
            border: 1px solid #d9dee7;
            border-radius: 10px;
            background: #fff;
            overflow: hidden;
        }

        .premium-qty-selector__btn {
            width: 42px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            background: #f8fafc;
            color: #374151;
            transition: background-color .2s ease, color .2s ease;
        }

        .premium-qty-selector__btn:hover {
            background: #eef2f7;
            color: #111827;
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
            gap: 10px;
        }

        .catalog-action-btn {
            min-height: 46px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 14px;
            border: 1px solid #d9dee7;
            border-radius: 10px;
            background: #fff;
            color: #1f2937;
            font-size: .95rem;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            transition: background-color .2s ease, border-color .2s ease, color .2s ease;
        }

        .catalog-action-btn:hover {
            color: #111827;
            text-decoration: none;
            background: #f8fafc;
            border-color: #cfd6e0;
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

        .catalog-action-btn--primary:hover {
            color: #fff;
            background: hsl(var(--base) / .9);
            border-color: hsl(var(--base) / .9);
        }

        .catalog-action-btn--secondary {
            background: #fff;
            border-color: hsl(var(--base) / .3);
            color: hsl(var(--base));
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

        .catalog-action-btn--block {
            width: 100%;
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
            opacity: .9;
        }

        .catalog-action-btn.is-success {
            border-color: #15803d;
            background: #eefbf3;
            color: #166534;
        }

        .catalog-action-btn--primary.is-success {
            background: #166534;
            border-color: #166534;
            color: #fff;
        }

        @media (max-width: 1199px) {
            .premium-cart-row {
                grid-template-columns: 1fr;
            }

            .product-card__actions,
            .premium-qty-selector {
                max-width: 100%;
            }
        }

        @media (max-width: 575px) {
            .header-cart-link {
                width: 40px;
                height: 40px;
            }

            .catalog-cart-actions {
                grid-template-columns: 1fr;
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
