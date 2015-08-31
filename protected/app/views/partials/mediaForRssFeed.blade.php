<div class="item-media-container">
    @if($media['type'] == 'quote')
        @include('partials.media-quote', array('media' => $item['media']))
    @elseif($media['type'] == 'photo')
        @include('partials.media-photo', array('media' => $item['media']))
    @endif
</div>
