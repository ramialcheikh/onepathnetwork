<div class="item-media-container">
    @if(empty($hideShareButtons))
        <div class="media-share-box">
            @include('partials.media-share-buttons', ['media'   =>  $item['media']])
        </div>
    @endif
    @if($media['type'] == 'quote')
        @include('partials.media-quote', array('media' => $item['media']))
    @elseif($media['type'] == 'video')
        @include('partials.media-video', array('media' => $item['media']))
    @elseif($media['type'] == 'audio')
        @include('partials.media-audio', array('media' => $item['media']))
    @elseif($media['type'] == 'photo')
        @include('partials.media-photo', array('media' => $item['media']))
    @endif
</div>
