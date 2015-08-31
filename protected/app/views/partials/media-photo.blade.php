<div class="item-media item-media-photo">
    <img class="" src="{{{asset($media['url'])}}}" alt=""/>
    @if(!empty($media['source']))
        <div class="media-image-source">
            {{__('source')}}:
            @if(Helpers::isValidUrl($media['source']))
                <a target="_blank" href="{{{Helpers::urlWithHttp($media['source'])}}}" rel="nofollow">{{{Helpers::getDomainFromUrl(Helpers::urlWithHttp($media['source']))}}}</a>
            @else
                {{{$media['source']}}}
            @endif
        </div>
    @endif
</div>