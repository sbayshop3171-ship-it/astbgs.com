@php
    $categories = App\Models\Category::active()
        ->with([
            'subcategories' => function ($query) {
                $query->active()
                    ->orderBy('id');
            },
        ])
        ->orderBy('id')
        ->get();

@endphp


<div class="wrapper-header">
    @include('Template::partials.header_top')

<header class="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">

            <a class="navbar-brand logo d-lg-none d-block" href="{{ route('home') }}">
                <img width="164" src="{{ siteLogo('dark') }}" alt="@lang('Image')">
            </a>

            <button class="navbar-toggler header-button" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span id="hiddenNav"><i class="las la-bars"></i></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <ul class="navbar-nav nav-menu me-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="{{ route('products') }}">@lang('All Items')</a>
                    </li>
                    @foreach ($categories ?? [] as $category)
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="{{ route('category.products', ['category' => $category->slug]) }}"
                                aria-expanded="false">
                                {{ $category->name }}
                                @if ($category->subcategories->count())
                                    <span class="nav-item__icon"><i class="las la-angle-down"></i></span>
                                @endif
                            </a>
                            @if ($category->subcategories->count())
                                <ul class="dropdown-menu">
                                    @foreach ($category->subcategories ?? [] as $subcategory)
                                        <li class="dropdown-menu__list">
                                            <a class="dropdown-item dropdown-menu__link"
                                                href="{{ route('category.products', ['category' => $category->slug, 'subcategory' => $subcategory->slug]) }}">{{ $subcategory->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <div class="d-flex align-items-center flex-wrap gap-2">
                    @auth
                        <a href="{{ route('user.transactions', ['balance_type' => \App\Constants\Status::BALANCE_TYPE_WALLET]) }}"
                            class="btn btn--sm btn-outline--base px-3">
                            <i class="las la-wallet"></i>
                            @lang('Wallet')
                            <span class="ms-1">{{ showAmount(auth()->user()->wallet_balance ?? 0) }}</span>
                        </a>
                        <a href="{{ route('user.deposit.index') }}" class="btn btn--sm btn-outline--light px-3">
                            <i class="las la-plus-circle"></i> @lang('Add Money')
                        </a>
                    @endauth
                    <a href="{{ route('plans') }}" class="btn btn--sm btn--base px-2">
                        <i class="las la-plus-circle"></i> @lang('Membership Plan')
                    </a>
                </div>
            </div>
        </nav>
    </div>
</header>
</div>
