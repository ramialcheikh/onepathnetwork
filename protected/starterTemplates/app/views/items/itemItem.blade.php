<div class="col-sm-6 col-md-6 @@singular@@-item">
	<div class="thumbnail">
	  <a href="{{ @@singular-pascalCase@@Helpers::view@@singular-pascalCase@@Url($@@singular@@)}}"><img data-src="{{ asset(!empty($@@singular@@->ogImages->main) ? $@@singular@@->ogImages->main .'_thumb.jpg' : $@@singular@@->image)}}" src="{{ asset(!empty($@@singular@@->ogImages->main) ? $@@singular@@->ogImages->main.'_thumb.jpg' : $@@singular@@->image)}}" alt="..."></a>
	  <div class="caption">
		<a href="{{ route('view@@singular-pascalCase@@', array('nameString' => @@singular-pascalCase@@Helpers::getUrlString($@@singular@@->topic), '@@singular@@Id' => $@@singular@@->id))}}"><h4>{{ $@@singular@@->topic }}</h4></a>
	  </div>
	</div>
</div>