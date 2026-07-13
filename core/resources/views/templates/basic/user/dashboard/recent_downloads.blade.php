<div class="col-12">
    <h6 class="mb-0">@lang('Recent Downloads')</h6>
</div>
<div class="col-md-12">
    <div class="card custom--card">
        <div class="card-body p-0">
            <div class="table-responsive">
                @if ($downloads->count() == 0)
                    <x-empty-list title="No downloads found" />
                @else
                    <div class="table-responsive">
                        <table class="table table--responsive--lg">
                            <thead>
                                <tr>
                                <tr>
                                    <th>@lang('Product | Date')</th>
                                    <th>@lang('Author')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($downloads ?? [] as $download)
                                    <tr>
                                        <td>
                                            <div class="table-product flex-align">
                                                <div class="table-product__thumb">
                                                    <x-product-thumbnail :product="$download->product" />
                                                </div>
                                                @if (isset($download->product))
                                                    <div class="table-product__content">
                                                        <a href="{{ route('product.details', $download->product?->slug) }}"
                                                            class="table-product__name">
                                                            {{ __(strLimit($download->product?->title, 20)) }}
                                                        </a>
                                                        {{ showDateTime($download->created_at) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('user.profile', $download->product?->author?->username) }}"
                                                class="link">
                                                {{ __($download->product?->author?->fullname) }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ __($download->product?->category?->name) }}
                                        </td>

                                        <td>
                                            <div class="d-flex flex-wrap gap-1 justify-content-end">
                                                <a href="{{ route('user.product.download', $download->product->slug) }}"
                                                    class="btn btn-outline--base btn--sm">
                                                    <i class="la la-download"></i> @lang('Download')
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
