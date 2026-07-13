@extends('Template::layouts.master')
@section('content')
    <div class="row gy-4">
        <div class="col-lg-8">
            <div class="follow-content">
                @forelse ($followers as $follower)
                    <x-follow :profile="$follower" />
                @empty
                    <x-empty-list title="No one followed yet" />
                @endforelse
                @if ($followers->hasPages())
                    <div class=" p-4">
                        {{ paginateLinks($followers) }}
                    </div>
                @endif
            </div>


        </div>

        <div class="col-lg-4 ps-xl-5">
            <div class="common-sidebar">
                @include('Template::partials.quick_upload')
                @include('Template::partials.email_support')
                @include('Template::partials.social_profile')
            </div>
        </div>
    </div>
@endsection
