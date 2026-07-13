@php
    $authorLevels = \App\Models\AuthorLevel::active()->get();
@endphp
<section class="profile-banner">
    <div class="container">
        @include('Template::user.profile.header')
        @include('Template::user.profile.tab')
    </div>
</section>
