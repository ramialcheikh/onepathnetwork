@foreach($list->content as $key => $item)
    <li class="item">
        <div class="item-head clearfix">
            <div class="item-number pull-left">{{($key+1)}}</div>
            <h2 class="item-title">{{{$item['title']}}}</h2>
        </div>
        @if(!empty($item['media']))
            @include('partials.media', array('media' => $item['media']))
        @endif
        @if(!empty($item['description']))
            <p class="item-description">{{ListHelpers::parseDescription($item['description'])}}</p>
        @endif
    </li>
@endforeach