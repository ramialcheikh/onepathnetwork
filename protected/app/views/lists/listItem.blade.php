<div class="col-xs-6 col-sm-6 @if($config['list']['gridType'] == 'three-column') col-md-4 @else col-md-6 @endif list-item animated">
	<div class="item-thumbnail">
        <div class="item-category">{{$list->category['name']}}</div>
	  <a class="image-link" href="{{ ListHelpers::viewListUrl($list)}}">
          <img src="{{ ListHelpers::getThumb($list) }}" alt="...">
      </a>
	  <div class="caption">
		<a href="{{ route('viewList', array('nameString' => ListHelpers::getUrlString($list->title), 'listId' => $list->id))}}">
            <h4 class="item-title">{{ $list->title }}</h4>
        </a>
        <div class="author-info">
          <a href="{{UserHelpers::userProfileUrl($list->creator)}}">
              <img class="author-photo img-circle" src="{{UserHelpers::getSquareProfilePic($list->creator)}}">{{$list->creator->name}}
            </a>
        </div>
          @if(ListHelpers::isMyList($list) && !empty($showListItemStatus))
              @if($list->isApproved())
                  <div class="item-status item-status-published">{{__('published')}}</div>
              @elseif($list->isAwaitingApproval())
                  <div class="item-status item-status-awaiting">{{__('awaitingApproval')}}</div>
              @elseif($list->isDisapproved())
                  <div class="item-status item-status-disapproved">{{__('disapproved')}}</div>
              @else
                  <div class="item-status item-status-not-published">{{__('notPublishedYet')}}</div>
              @endif
          @endif
	  </div>
	</div>
</div>