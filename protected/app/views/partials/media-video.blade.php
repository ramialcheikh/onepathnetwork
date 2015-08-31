<div class="item-media item-media-video">
    @if(ListHelpers::isMediaYoutubeVideo($media))
        <iframe class="media-video youtube-video" scrolling="no" style="border: 0px; display: block; margin: 0px auto;" src="{{{$media['url']}}}"></iframe>
    @elseif(ListHelpers::isMediaVimeoVideo($media))
        <iframe class="media-video vimeo-video" scrolling="no" style="border: 0px; display: block; margin: 0px auto;" src="{{{$media['url']}}}"></iframe>
    @endif
</div>