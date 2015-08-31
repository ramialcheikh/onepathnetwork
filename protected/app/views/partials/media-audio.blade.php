@if(ListHelpers::validateAudioMedia($media))
    <div class="item-media item-media-audio" data-audio-hash="{{ base64_encode($media['url']) }}">
    </div>
    <script>
        getSoundcloudEmbedCode('{{str_replace("'", "\'", $media['url'])}}' , function(err, embedCode){
            if(!err) {
                $('[data-audio-hash="{{ base64_encode($media['url']) }}"]').html(embedCode);
            }
        })
    </script>
@endif