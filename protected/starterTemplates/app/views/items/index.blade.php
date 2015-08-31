@extends('layout')

@section('foot')
@parent
<script src="{{ asset('bower_components/masonry/dist/masonry.pkgd.min.js') }}"></script>
<script src="{{ asset('bower_components/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
<script>
	$(function(){
		var $container = $('.items-row');
		imagesLoaded($container, function(){
			  var masonry = new Masonry( $container[0], {
				  itemSelector: '.item'
				});
		});
	});
</script>
@stop

@section('content')

	@if(!empty($widgets['above@@plural-pascalCase@@List']))
	<div class="@@plural@@-page-head-widget-section">
		@foreach($widgets['above@@plural-pascalCase@@List'] as $widget)
			{{$widget['content']}}
		@endforeach
	</div>
	@endif

	<h1 class="page-header text-center">
        @if(!empty($categoryName))
            {{$categoryName}}
        @else
        {{$mainHeading}}
        @endif
    </h1>
	
	
	@include('@@plural@@/@@plural@@List')
	{{ $@@plural@@->links('pagination::simple') }}
	
	@if(!empty($widgets['below@@plural-pascalCase@@List']))
	<div class="@@plural@@-page-foot-widget-section">
		@foreach($widgets['below@@plural-pascalCase@@List'] as $widget)
			{{$widget['content']}}
		@endforeach
	</div>
	@endif

@stop