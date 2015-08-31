@extends('layout')


@section('content')

    @if(!empty($widgets['homeHeader']))
        <div class="home-head-widget-section">
            @foreach($widgets['homeHeader'] as $widget)
                {{do_shortcode($widget['content'])}}
            @endforeach
        </div>
    @endif

    @include('lists/listsList')
    <div class="text-center">
        {{ $lists->links('pagination::simple') }}
    </div>

    @if(!empty($widgets['homeFooter']))
        <div class="home-foot-widget-section">
            @foreach($widgets['homeFooter'] as $widget)
                {{do_shortcode($widget['content'])}}
            @endforeach
        </div>
    @endif

@stop


@section('foot')
    @parent

@stop
