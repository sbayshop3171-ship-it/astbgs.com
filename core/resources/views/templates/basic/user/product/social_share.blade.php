<div class="social-share script-share">
    <button type="button" class="social-share__button">@lang('Share') <i class="icon-Share-Icon"></i></button>
    <div class="social-share__icons">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="share-link mb-0">@lang('Share')</h6>
            <button class="cross-icon">
                <i class="las la-times"></i>
            </button>
        </div>
        @php
            $url = route('product.details', $product->slug);
        @endphp

        <div class="social-icons my-4">
            <div class="item">
                <a class="link" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}&display=popup" target="_blank">
                    <span class="icon"><i class="fab fa-facebook-f"></i></span>
                    <p class="text">@lang('Facebook')</p>
                </a>
            </div>
            <div class="item">
                <a class="link" href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode($url) }}" target="_blank">
                    <span class="icon"> <i class="fab fa-linkedin-in"></i></span>
                    <p class="text">@lang('Linkedin')</p>
                </a>
            </div>
            <div class="item">
                <a class="link" href="https://www.instagram.com/share?url={{ urlencode($url) }}" target="_blank">
                    <span class="icon"> <i class="fab fa-instagram"></i></span>
                    <p class="text">@lang('Instagram')</p>
                </a>
            </div>
            <div class="item">
                <a class="link" href="https://twitter.com/intent/tweet?url={{ urlencode($url) }}" target="_blank">
                    <span class="icon"> <i class="fa-brands fa-x-twitter"></i></span>
                    <p class="text">@lang('x.com')</p>
                </a>
            </div>
        </div>

        <div class="input-group mt-5">
            <input class="form-control form--control-sm copy-input" name="copy_input" type="text" value="{{ $url }}" aria-label="" readonly />
            <span class="input-group-text copy-btn cursor-pointer" id="copyLinkBtn" data-link="{{ $url }}"><i class="far fa-copy"></i> </span>
        </div>

    </div>
</div>

@push('script')
    <script>
        "use strict";

        $('#copyLinkBtn').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var copybtn = $(this);
            var input = copybtn.closest('.input-group').find('.copy-input');

            if (input && input.select) {
                input.select();
                try {
                    copybtn.html(`<i class="far fa-copy text--success"></i>`);
                    document.execCommand('Copy');
                } catch (err) {
                    alert('Please press Ctrl/Cmd + C to copy');
                }
            }

            setTimeout(() => {
                copybtn.html(`<i class="far fa-copy"></i>`);
            }, 2000);
        });
    </script>
@endpush

@push('style')
    <style>
        .social-icons {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        .script-share .social-share__icons {
            min-width: 400px;
            padding: 30px;
            border-radius: 15px;
        }

        .social-share .form-control {
            background-color: #f8f9fd !important;
            opacity: 1;
            border: none !important;
        }

        .social-share .form-control:focus {
            box-shadow: none !important;
        }

        .social-share .input-group-text {
            background-color: #f8f9fd;
            border: none;
        }

        .social-icons .item {
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .social-icons .text {
            color: hsl(var(--heading-color));
            transition: 0.3s;
        }

        .social-icons .link {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 10px;
            align-content: center;
        }

        .social-icons .link:hover .text {
            color: hsl(var(--base));
        }

        .social-icons .link:hover .icon {
            background: hsl(var(--base));
            color: hsl(var(--white));
        }

        .social-icons .icon {
            border-radius: 50%;
            background-color: #f8f9fd;
            line-height: 1;
            font-size: 20px;
            width: 60px;
            height: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: hsl(var(--heading-color) / 0.8);
            transition: 0.3s;
            margin: 0 auto;
        }

        .share-title {
            color: hsl(var(--heading-color));
        }

        .cross-icon {
            background: #e4e5e5;
            height: 40px;
            width: 40px;
            line-height: 40px;
            border-radius: 50px;
            color: #737070;
        }

        .copy-input {
            background-color: #f8f9fd !important;
            padding: 18px;
            border-radius: 15px 0px 0px 15px;
            color: #5a5967
        }

        .copy-btn {
            padding-right: 20px;
            border-radius: 0px 15px 15px 0px;
            color: #5a5967
        }

        @media screen and (max-width: 419px) {
            .script-share .social-share__icons{
                min-width: 300px;
                padding: 20px;
            }

            .social-icons .icon {
                width: 50px;
                height: 50px;
            }

            .social-icons .text {
                display: none;
            }

            .cross-icon{
                height: 28px;
                width: 28px;
                line-height: 28px;
            }

            .input-group.mt-5{
                margin-top: 30px !important;
            }
        }
    </style>
@endpush
