@php
    $image = $author->avatar
        ? getImage(getFilePath('authorAvatar') . '/' . $author->avatar)
        : getImage('assets/images/avatar.png');

@endphp
<img src="{{ $image }}" class="author-avatar" alt="@lang('Author')">
