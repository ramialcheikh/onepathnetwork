<div class="row items-row {{$config['list']['gridType']}}">
	@forelse ($lists as $list)
		@include('lists/listItem')
	@empty
		<div class="col-md-12">
            <p class="text-center">{{__('noListsYet')}}</p>
        </div>
	@endforelse
</div>

<script>
    $('.list-item').addClass('fadeIn');
</script>
