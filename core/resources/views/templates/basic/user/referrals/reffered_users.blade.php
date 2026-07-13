@extends('Template::layouts.master')
@section('content')
    <div class="row gy-3">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h5 class="mb-0">{{ __($pageTitle) }}</h5>
                <x-search-form inputClass="form--control  search" btn="btn--base btn--sm" placeholder="Search.." />
            </div>
            <div class="card custom--card">
                <div class="card-body p-0">
                    @if ($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table--responsive--xl">
                                <thead>
                                    <tr>
                                        <th>@lang('Name')</th>
                                        <th>@lang('Username')</th>
                                        <th class="text-center">@lang('Email')</th>
                                        <th>@lang('Joined At')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $user->fullname }}</td>
                                            <td>
                                                <a href="{{ route('user.profile', $user->username) }}">
                                                    <span>@</span>{{ $user->username }}
                                                </a>
                                            </td>
                                            <td class="text-center">{{ $user->email }}</td>
                                            <td>{{ showDateTime($user->created_at, 'd M, Y h:i A') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if ($users->hasPages())
                            <div class="card-footer">
                                <div class="py-2">
                                    {{ paginateLinks($users) }}
                                </div>
                            </div>
                        @endif
                    @else
                        <x-empty-list title="No referred users found" />
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
