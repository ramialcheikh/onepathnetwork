@extends('layout')
@section('content')
@if(!$list->isApproved())
        <div class="alert alert-danger clearfix">
            @if(!empty($loggedInAdmin))
                <h4>Hey Admin!</h4>
            @endif
            <div class="btn btn-warning pull-right" data-dismiss="alert">{{__('Okay')}}</div>

            @if($list->isAwaitingApproval())
                <strong>{{__('thisListIsAwaitingApproval')}}</strong>

            @elseif($list->isDisapproved())
                <strong>{{__('thisListIsDisapproved')}}</strong>

            @else
                <strong>{{__('thisListIsNotPublishedYet')}}</strong>
            @endif

            @if(!empty($loggedInAdmin))
                <p>Only admin and the creator could view this list.</p>
            @else
                <p>{{ __('onlyYouCouldViewThis') }}</p>
            @endif
        </div>
@endif

@if(ListHelpers::isMyList($list))
    @if($list->hasPendingChanges())
        <div class="alert alert-warning clearfix">
            {{__('someChangesPendingApproval')}}
        </div>
    @endif
@endif

@if(!empty($widgets['aboveList']))
    <div class="above-list-widget-section">
        @foreach($widgets['aboveList'] as $widget)
            {{do_shortcode($widget['content'])}}
        @endforeach
    </div>
@endif

<script>
    (function() {
        window.getSoundcloudEmbedCode = function(url, callback) {
            $.getJSON('https://soundcloud.com/oembed?url=' + url + '&format=json').done(function(data) {
                if(data.html) {
                    callback(null, data.html);
                } else {
                    callback(true);
                }
            }).fail(function() {
                callback(true);
            });
        }
    })();

</script>


    <!-- View Item here -->
    <h1 class="list-main-title list-page-heading">{{{$list->title}}}</h1>
    <p class="list-main-description">{{ListHelpers::parseDescription($list->description)}}</p>
<div class="row">
    <div class="col-md-12 col-xs-12 col-sm-12">
        <div class="media list-creator-details">
            <div class="media-left pull-left">
                <a href="{{UserHelpers::userProfileUrl($listCreator)}}">
                    <img width="50" height="50" class="img-circle media-object" src="{{UserHelpers::getSquareProfilePic($listCreator, 50)}}" alt="{{{$listCreator->name}}}"/>
                </a>
            </div>
            <div class="media-body">
                <a class="creator-name-link" href="{{UserHelpers::userProfileUrl($listCreator)}}">
                    <span class="creator-name media-heading">{{{$listCreator->name}}}</span>
                </a>
                <p class="view-creator-lists-link-outer"><a class="view-creator-lists-link label label-success" href="{{UserHelpers::userProfileUrl($listCreator)}}"><small>{{__('viewLists')}}</small></a></p>
            </div>
        </div>
    </div>
    @if(ListHelpers::isMyList($list))
        <div class="col-md-12 col-xs-12 col-sm-12 creator-list-actions-block">
            <div class="creator-list-actions clearfix well well-sm">
                <a class="btn btn-inverse btn-sm pull-right" href="{{route('editList', ['listId' => $list->id])}}"><i class="fa fa-pencil"></i> {{__('editThisListBtnText')}}</a>
                <b>{{__('thisIsYourList')}}</b>
            </div>
        </div>
    @endif
</div>
<div class="top-share-buttons-section">
    @include('lists.partials.share-buttons', array('list' => $list))
</div>

    <div class="list-items-container">
        @if(!empty($showInterstitialAdNow))
            @include('partials.widgets', array('section' => 'interstitialAd'))
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <a class="btn btn-primary btn-lg btn-block skip-ad-btn" href="{{ListHelpers::viewListUrl($list, $showingItemNumber)}}">{{__('skipAd')}}</a>
                </div>
            </div>
        @else
            <ol class="items-list list-unstyled">
                @include('lists.partials.showListItems', ['list'    =>  $list])
            </ol>
            @if(!empty($oneItemPerPageMode))
                <div class="row">
                    @if($hasPreviousItem)
                        <div class="col-md-6 col-xs-6 pull-left">
                            <a class="previous-item-btn btn btn-default btn-block btn-lg" href="{{ListHelpers::viewListUrl($list, $showingItemNumber - 1)}}"><i class="fa fa-chevron-left pull-left"></i>{{__('previous')}}</a>
                        </div>
                    @endif
                    @if($hasNextItem)
                        <div class="col-md-6 col-xs-6 pull-right">
                            <a class="next-item-btn btn btn-primary btn-block btn-lg" href="{{ListHelpers::viewListUrl($list, $showingItemNumber + 1, $showInterstitialAdNext)}}">{{__('next')}} <i class="fa fa-chevron-right pull-right"></i></a>
                        </div>
                    @endif
                </div>
            @endif
        @endif
    </div>

    @if(!empty($widgets['belowList']))
    <div class="below-list-widget-section list-action-section">
        @foreach($widgets['belowList'] as $widget)
            {{do_shortcode($widget['content'])}}
        @endforeach
    </div>
    @endif

    <!-- Share list START -->
    <div class="row list-action-section">
        <div class="col-md-12 share-list-prompt text-center">
            <h2 class="share-list-prompt-title">{{__('shareListPromptMessage')}}</h2>
            @if(!empty($config['list']['showSharePreview']) && $config['list']['showSharePreview'] != "false")
                <div class="thumbnail share-list-preview">
                    <img class="list-preview-image" src="{{ListHelpers::getOgImage($list)}}">
                    <div class="caption">
                        <h5 class="list-title text-left">{{$list->title}}</h5>
                        @include('lists.partials.share-buttons', array('list' => $list))
                    </div>
                </div>
            @else
                @include('lists.partials.share-buttons', array('list' => $list))
            @endif
        </div>
    </div>
    <!-- Share list END -->

@if(!ListHelpers::isMyList($list))
    <!-- Edit list prompt START -->
    <div class="row list-action-section">
        <div class="col-md-12 col-xs-12">
            <div class="edit-this-list-prompt">
                <h3 class="edit-this-list-prompt-title">{{__('editListMessageTitle')}}</h3>
                <p>{{__('editListMessage')}}</p>
                <p>
                    <a class="btn btn-success btn-lg" href="{{route('createList', ['create-from' => $list->id])}}">{{__('editThisListBtnText')}}</a>
                </p>
            </div>
        </div>
    </div>
    <!-- Edit list prompt END -->
@endif

    <!-- List tags START -->
    <div class="list-tags list-action-section">
        <h4>{{__('tags')}}</h4>
        @foreach($listTags as $tag)
            <a class="list-tag label label-default" href="{{route('viewListsOfTag', [$tag->tag_slug])}}">{{{$tag->tag_name}}}</a>
        @endforeach
    </div>
    <!-- List tags END -->

@if(!empty($config['list']['showFacebookComments']) && $config['list']['showFacebookComments'] != "false")
    <div class="list-action-section">
        <div class="list-comments fb-comments" data-href="{{ListHelpers::viewListUrl($list)}}" data-width="100%" data-numposts="10" data-colorscheme="light"></div>
    </div>
@endif

<div id="belowListMoreListsBlock">
    <h4>{{__('youMayAlsoLike')}}</h4>
    {{do_shortcode(@$config['list']['youMayAlsoLikeShortCode'])}}
    <div class="text-center view-more-lists-btn-block">
        <a href="{{route('lists')}}" class="btn btn-primary"><span>{{__('viewMoreLists')}}</span></a>
    </div>
</div>

@stop

@section('foot')
@parent

    <script src="{{asset('js/social-sharing.js')}}"></script>
    <script>
        SocialSharing.parse();
    </script>

@stop