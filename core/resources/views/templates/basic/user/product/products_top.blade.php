@php
    $sortBy        = request('sort_by') ?? 'new_item';
    $filterOptions = [
        'new_item'     => 'New Item',
        'best_rated'   => 'Best Rated',
        'best_downloading' => 'Best Downloading',
    ];
@endphp

<div class="product-top flex-between gap-3">
    <button type="button" class="filter-btn flex-align gap-1">
        <span class="icon"><i class="icon-Filter"></i></span>@lang('Filter')
    </button>
    <div class="product-top__right flex-align">
        <ul class="filter-button-list gap-3">
            <li>
                <ul class="filter-button-list sort-options">
                    @foreach ($filterOptions as $key => $label)
                        <li class="filter-button-list__item">
                            <button type="button" 
                                class="filter-button-list__button sort-btn {{ $sortBy == $key ? 'active' : '' }}" 
                                data-sort="{{ $key }}">
                                {{ __($label) }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </li>
           
            <li class="view-buttons m-0">
                <button type="button" class="view-buttons__btn list-view-btn"><i class="icon-List-View"></i></button>
                <button type="button" class="view-buttons__btn grid-view-btn text--base"><i class="icon-Gride-View"></i></button>
            </li>
        </ul>
    </div>
</div>
