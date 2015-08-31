@extends('layout')


@section('content')

    @if(!empty($widgets['homeHeader']))
        <div class="home-head-widget-section">
            @foreach($widgets['homeHeader'] as $widget)
                {{$widget['content']}}
            @endforeach
        </div>
    @endif

    @include('@@plural@@/@@plural@@List')
    {{ $@@plural@@->links('pagination::simple') }}

    @if(!empty($widgets['homeFooter']))
        <div class="home-foot-widget-section">
            @foreach($widgets['homeFooter'] as $widget)
                {{$widget['content']}}
            @endforeach
        </div>
    @endif

@stop


@section('foot')
    @parent
    <script src="{{ asset('bower_components/masonry/dist/masonry.pkgd.min.js') }}"></script>
    <script src="{{ asset('bower_components/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
    {{--<script>
        $(function(){
            var $container = $('.items-row');
            imagesLoaded($container, function(){
                  var masonry = new Masonry( $container[0], {
                      itemSelector: '.item'
                    });
            });
        });
    </script>--}}
@stop
