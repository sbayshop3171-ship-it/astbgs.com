 <div class="author-reply">
     <div class="author-reply__thumb">
         <x-author-avatar :author="$reply->user" />
     </div>
     <div class="author-reply__content">
         <div class="flex-between flex-nowrap">
             <div>
                 <h6 class="author-reply__name">
                     <a href="{{ route('user.profile', $reply->user->username) }}">{{ $reply?->user?->fullname }}</a>
                 </h6>
                 <span class="author-reply__response mb-0">
                     @if ($reply->author_reply)
                         @lang('Author')
                     @endif
                 </span>
             </div>
             <span class="author-reply__time">{{ diffForHumans($reply->created_at) }}</span>
         </div>
         <p class="author-reply__desc mt-2">{{ $reply->text }}</p>
     </div>
 </div>
