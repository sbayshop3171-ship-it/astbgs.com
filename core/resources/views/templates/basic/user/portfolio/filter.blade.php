@php
    $sortBy = request('sort_by') ?? null;
    $status = intval(request('status') ?? -1);
    $orderBy = request()->order_by;
    $direction = request()->direction;
    $sortByKeys = [
        'title' => 'By Title',
        'published_at' => 'By Published',
        'last_updated' => 'By Updated',
        'total_download' => 'By Downloads',
        'avg_rating' => 'By Rating',
    ];
@endphp

<div class="profile-portfolio__top">
    <div class="d-flex justify-content-between flex-wrap gap-3">
        <x-search-form placeholder="Type product name" btn="btn--base btn--sm" :value="request()->search" />
       
            <div class="sort-by d-flex align-items-center">
                <select name="order_by" id="order_by" class="form-control form--control bg--white me-3 select2"
                    data-minimum-results-for-search="-1">
                    @foreach ($sortByKeys as $key => $label)
                        <option @selected($orderBy == $key) value="{{ $key }}">{{ __($label) }}</option>
                    @endforeach
                </select>

                <div class="view-buttons ms-0">
                    <button type="button" class="view-buttons__btn list-view-btn"><i
                            class="icon-List-View"></i></button>
                    <button type="button" class="view-buttons__btn grid-view-btn text--base"><i
                            class="icon-Gride-View"></i></button>
                </div>
            </div>
        
    </div>
</div>

@push('style')
    <style>
        .direction-link {
            font-size: 23px;
        }

        .select2-container--default .select2-results>.select2-results__options {
            width: 145px !important;
            background: #fff !important;
        }
    </style>
@endpush
