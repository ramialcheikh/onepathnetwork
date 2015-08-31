<div class="footer">
	&copy; {{$config['main']['siteName']}}
	@if(!empty($widgets['footer']))
		@foreach($widgets['footer'] as $widget)
			{{do_shortcode($widget['content'])}}
		@endforeach
	@endif

</div>