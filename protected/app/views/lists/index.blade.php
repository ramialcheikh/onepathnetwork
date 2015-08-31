@extends('layout')

@section('foot')
@parent

@stop

@section('content')

	@if(!empty($widgets['aboveListsList']))
	<div class="lists-page-head-widget-section">
		@foreach($widgets['aboveListsList'] as $widget)
            {{do_shortcode($widget['content'])}}
		@endforeach
	</div>
	@endif

	<h1 class="page-header">
        @if(!empty($categoryName))
            {{$categoryName}}
        @else
        {{{$mainHeading}}}
        @endif
    </h1>
	
	
	@include('lists/listsList')
	<div class="text-center">
        {{ $lists->links('pagination::simple') }}
    </div>
	
	@if(!empty($widgets['belowListsList']))
	<div class="lists-page-foot-widget-section">
		@foreach($widgets['belowListsList'] as $widget)
            {{do_shortcode($widget['content'])}}
		@endforeach
	</div>
	@endif

@stop