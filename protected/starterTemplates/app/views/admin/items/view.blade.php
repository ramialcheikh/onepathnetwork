@extends('admin/layout')


@section('content')

	<style>
		.share-rate-low {
			color: #999;
		}
		.share-rate-medium {
			color: #87b45e;
		}
		.share-rate-fair {
			color: #ed6300;
		}
		.share-rate-high {
			color: #ef1a00;
		}
	</style>


<h2 class="page-header">
	Viewing @@plural@@
</h2>

<div class="row">
	<div class="col-md-12">
		@if($search)
			<h4>Searching for <small>"{{$search}}"</small></h4>
		@endif
		<div class="table-responsive">
		@if(!empty($@@plural@@))
			<table class="table table-bordered table-striped">
				<tr>
					<td colspan="42" class="active">
						<form class="form-inline" action="{{route('adminView@@plural-pascalCase@@')}}">
                            <a class="btn btn-success" href="{{route('adminCreate@@singular-pascalCase@@')}}"><i class="fa fa-plus"></i> Create new @@singular-pascalCase@@</a> &nbsp;
							<div class="form-group">
								<input type="text" name="search" class="form-control" id="searchField" placeholder="Search @@plural@@" value="{{$search}}">
							</div>
							<input type="submit" class="btn btn-default" value="Search"/>
						</form>
					</td>
				</tr>
				<tr class="">

					<th style="width: 50px;">Photo</th>
					<th>Topic</th>
					<th>
						<a @if($sort === 'created_at') class="text-danger" @endif href="{{ Helpers::getUrlWithQuery(array('sort' => 'created_at', 'sortType' => ($sortType == 'asc') ? 'desc' : 'asc')) }}">Created on @if($sort === 'created_at')

								@if($sortType === 'asc')
									<i class="fa fa-caret-up">
										@else
											<i class="fa fa-caret-down">
												@endif
											</i>
								@endif
						</a>
					</th>
					<th>Active</th>
					<th>Actions</th>
				</tr>
		@forelse (@$@@plural@@ as $@@singular@@)
			<tr>

				<td><a target="_blank" href="{{ @@singular-pascalCase@@Helpers::view@@singular-pascalCase@@Url($@@singular@@)}}"><img src="{{ asset($@@singular@@->image) }}" alt="" width="120"></a></td>
				<td width="40%"><h5 style="line-height: 1.5em;"><a style="color: #333;" target="_blank" href="{{ @@singular-pascalCase@@Helpers::view@@singular-pascalCase@@Url($@@singular@@)}}">{{$@@singular@@->topic}}</a></h5></td>
				<td>{{ Helpers::prettyTime($@@singular@@->created_at, false)}}</td>
				<td>@if($@@singular@@->active)
					<div class="label label-success">Active</div>
				@else
					<div class="label label-danger">Inactive</div>
				@endif
				</td>
				<td>
					<a href="{{ route('adminCreate@@singular-pascalCase@@', array('action' => 'edit', '@@singular@@Id' => $@@singular@@->id)) }}" class="btn btn-sm btn-success" role="button" data-toggle="tooltip" title="Edit @@singular@@"><i class="fa fa-edit"></i></a>
					<a href="javascript:void(0)" data-@@singular@@-id="{{$@@singular@@->id}}" class="btn btn-sm btn-danger @@singular@@-delete-btn" role="button" title="Delete @@singular@@" data-toggle="tooltip" title="Delete @@singular@@"><i class="fa fa-times"></i></a>
                    <a href="{{ route('adminCreate@@singular-pascalCase@@', array('action' => 'create', 'duplicate-@@singular@@' => $@@singular@@->id)) }}" class="btn btn-sm btn-info" role="button" data-toggle="tooltip" title="Duplicate @@singular@@"><i class="fa fa-copy"></i></a>
					<!--<a href="#" class="btn btn-danger" role="button"><i class="fa fa-trash-o"></i></a>-->
				</td>
			</tr>
		@empty
			<tr><td colspan="42" class="text-center">
					<br/>
					@if($search)
						<div class="alert alert-info"><b>No @@plural@@ matching "{{$search}}"</b></div>
					@else
						<div class="alert alert-info"><b>No @@plural@@ yet!</b></div>
					@endif
				</td></tr>
		@endforelse
	</table>
		</div>
		<br>{{ $@@plural@@->links() }}
	@endif
	</div>
	<script>
		$(function(){
			$('body').on('click', '.@@singular@@-delete-btn', function(){
				var @@singular@@Id = $(this).data('@@singular@@Id');
				dialogs.confirm("Are you sure to delete the @@singular@@?", function(confirm){
					if(confirm){
						$.post('{{route('adminDelete@@singular-pascalCase@@')}}', {
							@@singular@@Id : @@singular@@Id
						}).success(function (res) {
							if (res.success) {
								dialogs.success('@@singular-pascalCase@@ Deleted', function () {
									window.location.href = '{{route('adminView@@plural-pascalCase@@')}}';
								});
							} else if (res.error) {
								dialogs.error('Error occured\n' + res.error);
							} else {
								dialogs.error('Some Error occured');
							}
						}).fail(function (res) {
							dialogs.error(res.responseText);
						});
					}
				})
			});
		});


        $(function(){
           $('[data-toggle="tooltip"]').tooltip();
        });
	</script>
</div>

@stop