<p>
    {{$list->description}}
</p>

<ol>
    @foreach($list->content as $key => $item)
        <li>
            @if(!empty($item['media']))
                @include('partials.mediaForRssFeed', array('media' => $item['media'], 'hideShareButtons' =>  true))
            @endif
        </li>
    @endforeach
</ol>