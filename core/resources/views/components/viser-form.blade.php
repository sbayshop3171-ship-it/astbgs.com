<div class="row">
    @foreach($formData as $data)
    <div class="col-md-{{ (isset($data->width) && $data->width) ? $data->width : '12' }}">
            <div class="form-group">
                <label class="form-label">{{ __($data->name) }} @if(isset($data->instruction) && $data->instruction) <span data-bs-toggle="tooltip" title="{{ __($data->instruction) }}"><i class="fas fa-info-circle"></i></span> @endif @if($data->is_required == 'required' && ($data->type == 'checkbox' || $data->type == 'radio')) <span class="text--danger">*</span> @endif </label>
                @if($data->type == 'text')
                    @php $fieldValue = old($data->label, $editData[$data->label] ?? null); @endphp
                    <input type="text"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    value="{{ $fieldValue }}"
                    @if($data->is_required == 'required') required @endif
                    >
                @elseif($data->type == 'url')
                    @php $fieldValue = old($data->label, $editData[$data->label] ?? null); @endphp
                    <input type="url"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    value="{{ $fieldValue }}"
                    @if($data->is_required == 'required') required @endif
                    >
                @elseif($data->type == 'email')
                    @php $fieldValue = old($data->label, $editData[$data->label] ?? null); @endphp
                    <input type="email"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    value="{{ $fieldValue }}"
                    @if($data->is_required == 'required') required @endif
                    >
                @elseif($data->type == 'datetime')
                    @php $fieldValue = old($data->label, $editData[$data->label] ?? null); @endphp
                    <input type="datetime-local"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    value="{{ $fieldValue }}"
                    @if($data->is_required == 'required') required @endif
                    >
                @elseif($data->type == 'date')
                    @php $fieldValue = old($data->label, $editData[$data->label] ?? null); @endphp
                    <input type="date"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    value="{{ $fieldValue }}"
                    @if($data->is_required == 'required') required @endif
                    >
                @elseif($data->type == 'time')
                    @php $fieldValue = old($data->label, $editData[$data->label] ?? null); @endphp
                    <input type="time"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    value="{{ $fieldValue }}"
                    @if($data->is_required == 'required') required @endif
                    >
                @elseif($data->type == 'number')
                    @php $fieldValue = old($data->label, $editData[$data->label] ?? null); @endphp
                    <input type="number"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    value="{{ $fieldValue }}"
                    step="any"
                    @if($data->is_required == 'required') required @endif
                    >
                @elseif($data->type == 'textarea')
                    @php $fieldValue = old($data->label, $editData[$data->label] ?? null); @endphp
                    <textarea
                        class="form-control form--control"
                        name="{{ $data->label }}"
                        @if($data->is_required == 'required') required @endif
                    >{{ $fieldValue }}</textarea>
                @elseif($data->type == 'select')
                    @php $fieldValue = old($data->label, $editData[$data->label] ?? null); @endphp
                    <select
                        class="form-select form--control select2" data-minimum-results-for-search="-1"
                        name="{{ $data->label }}"
                        @if($data->is_required == 'required') required @endif
                    >
                        <option value="">@lang('Select One')</option>
                        @foreach ($data->options as $item)
                            <option value="{{ $item }}" @selected($item == $fieldValue)>{{ __($item) }}</option>
                        @endforeach
                    </select>
                @elseif($data->type == 'checkbox')
                    @php $fieldValue = old($data->label, $editData[$data->label] ?? []); @endphp
                    <div class="d-flex gap-3 flex-wrap">
                        @foreach($data->options as $option)
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    name="{{ $data->label }}[]"
                                    type="checkbox"
                                    value="{{ $option }}"
                                    id="{{ $data->label }}_{{ titleToKey($option) }}"
                                    @checked(in_array($option, (array) $fieldValue))
                                >
                                <label class="form-check-label" for="{{ $data->label }}_{{ titleToKey($option) }}">{{ $option }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="checkbox-required-error text--danger"></div>
                @elseif($data->type == 'radio')
                    @php $fieldValue = old($data->label, $editData[$data->label] ?? null); @endphp
                    <div class="d-flex gap-3 flex-wrap">
                        @foreach($data->options as $option)
                            <div class="form-check">
                                <input
                                class="form-check-input"
                                name="{{ $data->label }}"
                                type="radio"
                                value="{{ $option }}"
                                id="{{ $data->label }}_{{ titleToKey($option) }}"
                                @checked($option == $fieldValue)
                                >
                                <label class="form-check-label" for="{{ $data->label }}_{{ titleToKey($option) }}">{{ $option }}</label>
                            </div>
                        @endforeach
                    </div>
                @elseif($data->type == 'file')
                    <input
                    type="file"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    @if($data->is_required == 'required') required @endif
                    accept="@foreach(explode(',',$data->extensions) as $ext) .{{ $ext }}, @endforeach"
                    >
                    <pre class="text--base mt-1">@lang('Supported mimes'): {{ $data->extensions }}</pre>
                @endif
            </div>
        </div>
    @endforeach
</div>
@push('script')
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
@endpush
