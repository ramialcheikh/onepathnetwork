<div class="sharing-buttons-block">
    <div class="row sharing-buttons-set">
        <div class="col-md-5 col-xs-5 share-list-button-box">
            <a class="btn btn-block btn-social-facebook social-sharing-btn" data-social-network="facebook" data-url="{{ListHelpers::viewListUrl($list)}}" data-title="{{{$list->title}}}" href=""><i class="fa fa-facebook"></i>
            <span class="hidden-sm hidden-xs">
                {{__('shareOnFB')}}
            </span>
            <span class="hidden-md hidden-lg">
                {{__('share')}}
            </span>
            </a>
        </div>
        <div class="col-md-5 col-xs-5 share-list-button-box">
            <a class="btn btn-block btn-social-twitter social-sharing-btn" data-social-network="twitter" data-url="{{ListHelpers::viewListUrl($list)}}" data-title="{{{$list->title}}}" href=""><i class="fa fa-twitter"></i>
            <span class="hidden-sm hidden-xs">
                {{__('shareOnTwitter')}}
            </span>
            <span class="hidden-md hidden-lg">
                {{__('tweet')}}
            </span>
            </a>
        </div>
        <div class="col-md-2 col-xs-2 share-list-more-btn"><a class="btn btn-block btn-default" href=""><i class="fa fa-share"></i></a></div>
    </div>
    <div class="row more-sharing-buttons-set">
        <div class="col-md-3 col-xs-3 share-list-button-box"><a class="btn btn-block btn-social-googleplus social-sharing-btn" data-social-network="googleplus" data-url="{{ListHelpers::viewListUrl($list)}}" data-title="{{{$list->title}}}" href=""><i class="fa fa-google-plus"></i></a></div>
        <div class="col-md-3 col-xs-3 share-list-button-box"><a class="btn btn-block btn-social-pinterest social-sharing-btn" data-social-network="pinterest" data-url="{{ListHelpers::viewListUrl($list)}}" data-title="{{{$list->title}}}" href=""><i class="fa fa-pinterest"></i></a></div>
        <div class="col-md-3 col-xs-3 share-list-button-box"><a class="btn btn-block btn-social-tumblr social-sharing-btn" data-social-network="tumblr" data-url="{{ListHelpers::viewListUrl($list)}}" data-title="{{{$list->title}}}" href=""><i class="fa fa-tumblr"></i></a></div>
        <div class="col-md-3 col-xs-3 share-list-button-box"><a class="btn btn-block btn-social-reddit social-sharing-btn" data-social-network="reddit" data-url="{{ListHelpers::viewListUrl($list)}}" data-title="{{{$list->title}}}" href=""><i class="fa fa-reddit"></i></a></div>
    </div>
</div>

<script>
    $(function() {
        var shareButtonsBlocks = $('.sharing-buttons-block');
        shareButtonsBlocks.each(function() {
            if($(this).data('initiated'))
                return;
            var shareButtonsBlock = $(this);
            $(this).find('.share-list-more-btn').click(function(e) {
                shareButtonsBlock.toggleClass('show-more');
                e.preventDefault();
                return false;
            });
            $(this).data('initiated', true);
        });
    });
</script>