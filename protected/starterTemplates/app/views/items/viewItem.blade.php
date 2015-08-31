@extends('layout')
@section('content')
@if(!empty($@@singular@@Inactive))
    <div class="alert alert-danger clearfix" style="margin-top: 30px;">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <h4><strong>Hey Admin!</strong></h4>
        <div class="btn btn-red pull-right" data-dismiss="alert"><span>Okay</span></div>
        <strong>This @@singular-pascalCase@@ is currently inactive.</strong>
        <p>Only you could view this.</p>
    </div>
@endif
<script>
    var @@singular-pascalCase@@Data = {{ json_encode($@@singular@@) }};
    var @@singular-pascalCase@@Meta = {
        view@@singular-pascalCase@@Url: '{{@@singular-pascalCase@@Helpers::view@@singular-pascalCase@@Url($@@singular@@)}}'
    };
</script>

@if(!empty($widgets['above@@singular-pascalCase@@Question']))
    <div class="below-@@singular@@-widget-section">
        @foreach($widgets['above@@singular-pascalCase@@Question'] as $widget)
            {{$widget['content']}}
        @endforeach
    </div>
@endif

        <!-- View Item here -->

@if(!empty($widgets['below@@singular-pascalCase@@']))
<div class="below-@@singular@@-widget-section">
	@foreach($widgets['below@@singular-pascalCase@@'] as $widget)
		{{$widget['content']}}
	@endforeach
</div>
@endif
@if(!empty($config['@@singular@@']['showFacebookComments']) && $config['@@singular@@']['showFacebookComments'] != "false")
<div style="margin-top: 40px;">
    <h3>{{__('comments')}}</h3>
    <div class="fb-comments" data-href="{{@@singular-pascalCase@@Helpers::view@@singular-pascalCase@@Url($@@singular@@)}}" data-width="100%" data-numposts="10" data-colorscheme="light"></div>
</div>
@endif

<div id="below@@singular-pascalCase@@More@@plural-pascalCase@@Block">
	<h3 class="text-center"><strong>{{__('youMayAlsoLike')}}</strong></h3>
	@include('@@plural@@/@@plural@@List')
	@if(is_array($related@@plural-pascalCase@@) && count($related@@plural-pascalCase@@))
        <div class="text-center">
            <br/>
            <a href="{{route('@@plural@@')}}" class="btn btn-primary"><span>{{__('viewMore@@plural-pascalCase@@')}}</span></a>
        </div>
    @endif
</div>

@stop

@section('foot')
@parent
<script src="{{ asset('bower_components/masonry/dist/masonry.pkgd.min.js') }}"></script>
<script src="{{ asset('bower_components/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>

@if(App::isLocal())
<script data-main="{{ asset('js/@@singular@@/init.js')}}" src="{{ asset('bower_components/requirejs/require.js')}}"></script>
@else
<script data-main="{{ asset('js/@@singular@@/bundle.min.js')}}" src="{{ asset('bower_components/requirejs/require.js')}}"></script>
@endif

<script>
	$(function(){
		var $container = $('.@@singular@@-items-row');
		imagesLoaded($container, function(){
			  var masonry = new Masonry( $container[0], {
				  itemSelector: '.@@singular@@-item'
				});
		});
	});
</script>
@stop