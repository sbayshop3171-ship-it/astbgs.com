@extends('Template::layouts.master')
@section('content')
    <div class="row gy-3">
        <div class="col-lg-12">
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if ($replies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table--responsive--xl">
                                <thead>
                                    <tr>
                                        <th>@lang('Replied By')</th>
                                        <th>@lang('Message')</th>
                                        <th>@lang('Replied At')</th>
                                        <th>@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($replies as $reply)
                                        <tr>
                                            <td>
                                                @php $user = $reply->user; @endphp
                                                <div>
                                                    <span class="fw-bold">{{ $user->fullname }}</span>
                                                    <br>
                                                    <span>
                                                        <a class="text--base" href="{{ route('user.profile', $user->username) }}"><span>@</span>{{ $user->username }}</a>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ strLimit(__($reply->text), 100) }}
                                                    @if (strlen($reply->text) > 100)
                                                        <a href="javascript:void(0)" data-message="{{ __($reply->text) }}" class="readMoreBtn">@lang('read more')</a>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                {{ showDateTime($reply->created_at) }}
                                            </td>
                                            <td>
                                                <div class="button--group">
                                                    <button class="btn btn-outline--danger btn--sm confirmationBtn" data-question="@lang('Are you sure to delete the reply?')" data-action="{{ route('user.author.comments.replies.delete', $reply->id) }}">
                                                        <i class="la la-trash-alt"></i> @lang('Delete')
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($replies->hasPages())
                            <div class="py-4">
                                {{ paginateLinks($replies) }}
                            </div>
                        @endif
                    @else
                        <x-empty-list title="No replies found" />
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal frontend="true" />

    <div id="hoverTooltip" class="hover-tooltip">
        <div id="tooltipContent" class="tooltip-content"></div>
    </div>
@endsection

@push('style')
    <style>
        .hover-tooltip {
            position: absolute;
            display: none;
            background: #fff;
            color: #333;
            padding: 12px 5px 5px 16px;
            border-radius: 5px;
            font-size: 14px;
            z-index: 9999;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            max-width: 450px;
            pointer-events: auto;
        }

        .tooltip-content {
            max-height: 350px;
            overflow-y: auto;
        }

        .hover-tooltip.arrow-top::before {
            content: "";
            position: absolute;
            top: -8px;
            /* arrow above tooltip */
            left: 20px;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid #fff;
            filter: drop-shadow(0 -1px 1px rgba(0, 0, 0, 0.05));
            pointer-events: none;
        }

        .hover-tooltip.arrow-bottom::before {
            content: "";
            position: absolute;
            bottom: -8px;
            /* arrow below tooltip */
            left: 20px;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 8px solid #fff;
            filter: drop-shadow(0 1px 1px rgba(0, 0, 0, 0.05));
            pointer-events: none;
        }

        @media (max-width: 480px) {
            .hover-tooltip {
                max-width: 90vw;
            }
        }
    </style>
@endpush

@push('script')
    <script>
        "use strict";

        const tooltip = document.getElementById('hoverTooltip');
        let tooltipTimeout = null;

        const showTooltip = (btn) => {
            const container = document.getElementById('tooltipContent')
            container.innerText = btn.dataset.message;
            tooltip.style.display = 'block';
            tooltip.style.visibility = 'hidden';

            const rect = btn.getBoundingClientRect();
            const scrollTop = window.scrollY || document.documentElement.scrollTop;
            const scrollLeft = window.scrollX || document.documentElement.scrollLeft;

            const tooltipHeight = tooltip.offsetHeight;
            const tooltipWidth = tooltip.offsetWidth;

            let top = rect.bottom + scrollTop + 12;
            let left = rect.left + scrollLeft;

            // If not enough space below, show above
            if (rect.bottom + tooltipHeight > window.innerHeight) {
                top = rect.top + scrollTop - tooltipHeight - 12;

                tooltip.classList.remove('arrow-top');
                tooltip.classList.add('arrow-bottom');
            } else {
                tooltip.classList.remove('arrow-bottom');
                tooltip.classList.add('arrow-top');
            }

            // Clamp to left/right
            if (left + tooltipWidth > window.innerWidth) {
                left = window.innerWidth - tooltipWidth - 10;
            }
            if (left < 10) left = 10;

            tooltip.style.top = `${top}px`;
            tooltip.style.left = `${left}px`;
            tooltip.style.visibility = 'visible';
        };


        document.querySelectorAll('.readMoreBtn').forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                showTooltip(btn);
            });

            btn.addEventListener('mouseleave', () => {
                tooltipTimeout = setTimeout(() => {
                    tooltip.style.display = 'none';
                }, 200);
            });
        });

        tooltip.addEventListener('mouseenter', () => {
            clearTimeout(tooltipTimeout);
        });

        tooltip.addEventListener('mouseleave', () => {
            tooltip.style.display = 'none';
        });
    </script>
@endpush
