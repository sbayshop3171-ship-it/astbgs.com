@php
    $socialIcons = getContent('social_icon.element', orderById: true);
    $policyPages = getContent('policy_pages.element', orderById: true);
    $totalProducts = App\Models\Product::count();
    $totalDownload = App\Models\Earning::sum('download_count');
    $totalMembers = App\Models\User::count();
    $categories = \App\Models\Category::active()
        ->featured()
        ->withCount(['products as total_products' => function($query) {
            $query->approved();
        }])
        ->having('total_products', '>', 0)
        ->orderBy('total_products', 'DESC')
        ->limit(3)
        ->get();
@endphp

<footer class="footer">
    <img src="{{ asset($activeTemplateTrue . 'images/footer-shape2.png') }}" alt="@lang('Image')"
        class="footer__shape three">
    <img src="{{ asset($activeTemplateTrue . 'images/footer-shape2.png') }}" alt="@lang('Image')"
        class="footer__shape two">
    <div class="pb-60 pt-120">
        <div class="container">
            <div class="row justify-content-between gy-5">
                <div class="col-xl-3 col-lg-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <div class="footer-item__logo">
                            <a href="{{ route('home') }}"> <img src="{{ siteLogo() }}" alt="@lang('Image')"></a>
                        </div>

                        <ul class="footer-market-fact-items">
                            <li class="market_fact__footer-menu__item">
                                <div class="market-fact-items__text">
                                    <span class="market-fact-items__icon text--base"> <i class="las la-users"></i>
                                    </span>
                                    <span class="market-fact-items__count text--base">{{ $totalMembers }}</span>
                                    <span class="market-fact-items__title">@lang(str()->plural('Member', $totalMembers))</span>
                                </div>
                            </li>
                            <li class="market_fact__footer-menu__item">
                                <div class="market-fact-items__text">
                                    <span class="market-fact-items__icon text--base"> <i class="las la-download"></i>
                                    </span>
                                    <span class="market-fact-items__count text--base">{{ $totalDownload }}</span>
                                    <span class="market-fact-items__title">@lang(str()->plural('Download', $totalDownload)) </span>

                                </div>
                            </li>
                            <li class="market_fact__footer-menu__item">
                                <div class="market-fact-items__text">
                                    <span class="market-fact-items__icon text--base"> <i class="las la-file-alt"></i>
                                    </span>
                                    <span class="market-fact-items__count text--base">{{ $totalProducts }}</span>
                                    <span class="market-fact-items__title">@lang(str()->plural('Asset', $totalProducts)) </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h6 class="footer-item__title">@lang('Categories')</h6>
                        <ul class="footer-menu">
                            @foreach ($categories as $category)
                                <li class="footer-menu__item">
                                    <a href="{{ route('products', ['category' => $category->slug]) }}"
                                        class="footer-menu__link"><i class="las la-angle-double-right"></i> {{ __($category->name) }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h6 class="footer-item__title">@lang('Quick Link')</h6>
                        <ul class="footer-menu">
                            <li class="footer-menu__item">
                                @auth
                                    <a class="footer-menu__link" href="{{ route('user.home') }}">
                                       <i class="las la-angle-double-right"></i> @lang('Dashboard')
                                    </a>
                                @else
                                    <a class="footer-menu__link" href="{{ route('user.register') }}">
                                       <i class="las la-angle-double-right"></i> @lang('Create Account')
                                    </a>
                                @endauth
                            </li>

                            @foreach ($pages as $page)
                                @php $isActive = route('pages', [$page->slug]) == request()->url(); @endphp
                                <li class="footer-menu__item">
                                    <a class="footer-menu__link @if ($isActive) active @endif" href="{{ route('pages', [$page->slug]) }}"><i class="las la-angle-double-right"></i> {{ __($page->name) }}</a>
                                </li>
                            @endforeach

                            <li class="footer-menu__item">
                                <a class="footer-menu__link" href="{{ route('contact') }}">
                                   <i class="las la-angle-double-right"></i> @lang('Contact Us')
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-xl-2 col-lg-3 col-sm-6 col-xsm-6">
                    <div class="footer-item">
                        <h6 class="footer-item__title">@lang('Legal Stuff')</h6>
                        <ul class="footer-menu">

                            @foreach ($policyPages as $policy)
                                <li class="footer-menu__item">
                                    <a class="footer-menu__link" href="{{ route('policy.pages', $policy->slug) }}">
                                       <i class="las la-angle-double-right"></i> {{ __($policy->data_values->title) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom-footer py-4">
        <div class="container">
            <div class="bottom--wrapper d-flex justify-content-between gap-3 flex-wrap text-center align-items-center">
                <p class="bottom-footer__text text-white fs-14">
                    &copy; {{ date('Y') }} <a href="{{ route('home') }}" class="text-white fw-bold">{{ __(gs('site_name')) }}</a>. @lang('All Rights Reserved')
                </p>
                <ul class="social-list">
                    @foreach ($socialIcons as $socialIcon)
                        <li class="social-list__item"><a title="{{ __($socialIcon->data_values->title) }}"
                                href="{{ $socialIcon?->data_values?->url }}" target="_blank"
                                class="social-list__link flex-center">@php echo $socialIcon?->data_values?->social_icon @endphp</a> </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</footer>
