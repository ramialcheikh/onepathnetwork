<div class="row items-row">
	@forelse ($@@plural@@ as $@@singular@@)
		@include('@@plural@@/@@singular@@Item')
	@empty
		<div class="col-md-12">
            <p class="text-center">{{__('no@@plural-pascalCase@@Yet')}}</p>
        </div>
	@endforelse
</div>
