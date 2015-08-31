@if(!empty($widgets['sideBar']))
	@foreach($widgets['sideBar'] as $widget)
		<div class="sidebar-item">
		{{do_shortcode($widget['content'])}}
		</div>
	@endforeach
@endif
