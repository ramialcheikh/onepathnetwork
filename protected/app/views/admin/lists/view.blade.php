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
	Viewing lists
</h2>

<div class="row">
	<div class="col-md-12">
		@if($search)
			<h4>Searching for <small>"{{$search}}"</small></h4>
		@endif
		<div class="table-responsive">
		@if(!empty($lists))
			<table class="table table-bordered table-striped">
				<tr>
					<td colspan="42" class="active">
						<form class="form-inline" action="{{route('adminViewLists')}}">
							<div class="form-group">
								<input type="text" name="search" class="form-control" id="searchField" placeholder="Search lists" value="{{$search}}">
							</div>
							<input type="submit" class="btn btn-default" value="Search"/>
						</form>
					</td>
				</tr>
				<tr class="">

					<th style="width: 50px;">Photo</th>
					<th>Topic</th>
                    <th>Approval Status</th>
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
					<th>Actions</th>
				</tr>
		@forelse (@$lists as $list)
			<tr>

				<td><a target="_blank" href="{{ ListHelpers::viewListUrl($list)}}"><img src="{{ asset($list->image) }}" alt="" width="120"></a></td>
				<td width="40%"><h5 style="line-height: 1.5em;"><a style="color: #333;" target="_blank" href="{{ ListHelpers::viewListUrl($list)}}">{{$list->title}}</a></h5>
                    <div class="clearfix">
                        @if($list->status == 'disapproved' || $list->status == 'awaiting_approval')
                            <form action="{{route('approveList')}}" method="post" class="pull-left">
                                <input name="list-id" type="hidden" value="{{$list->id}}"/>
                                <input class="btn btn-success btn-sm" type="submit" value="Approve List"></input>
                            </form>
                        @endif
                        @if($list->status == 'approved' || $list->status == 'awaiting_approval')
                            <form action="{{route('disapproveList')}}" method="post" class="pull-left" style="margin-left: 10px;">
                                <input name="list-id" type="hidden" value="{{$list->id}}"/>
                                <input class="btn btn-danger btn-sm" type="submit" value="Disapprove List"></input>
                            </form>
                        @endif
                    </div>
                    @if($list->pendingChanges)
                        <div class="clearfix" style="margin-top: 10px;">
                            <a style="margin-left: 5px;" target="_blank" class="btn btn-default btn-sm pull-left" href="{{ListHelpers::viewListUrl($list)}}?preview-pending-changes=1">Preview changes</a>
                            <form style="margin-left: 5px;" action="{{route('approveList')}}" method="post" class="pull-left">
                                <input name="list-id" type="hidden" value="{{$list->id}}"/>
                                <input class="btn btn-success btn-sm" type="submit" value="Approve changes"></input>
                            </form>
                            <form action="{{route('disapproveList')}}" method="post" class="pull-left" style="margin-left: 10px;">
                                <input name="list-id" type="hidden" value="{{$list->id}}"/>
                                <input name="changes-only" type="hidden" value="true"/>
                                <input class="btn btn-danger btn-sm" type="submit" value="Disapprove Changes"></input>
                            </form>
                        </div>
                    @endif
                </td>
                <td width="100px">
                    <div class="label
                        @if($list->status == 'approved')
                        label-success
                        @elseif($list->status == 'disapproved')
                        label-danger
                        @elseif($list->status == 'awaiting_approval')
                        label-warning
                        @else
                        label-default
                        @endif
                         ">{{ $list->status ? $list->status : "not_submitted"}}
                    </div>
                </td>
				<td>{{ Helpers::prettyTime($list->created_at, false)}}</td>
				<td>
					<a href="{{ route('editList', array('listId' => $list->id)) }}" class="btn btn-sm btn-success" role="button" data-toggle="tooltip" title="Edit list"><i class="fa fa-edit"></i></a>
					<a href="javascript:void(0)" data-list-id="{{$list->id}}" class="btn btn-sm btn-danger list-delete-btn" role="button" title="Delete list" data-toggle="tooltip" title="Delete list"><i class="fa fa-times"></i></a>
					<!--<a href="#" class="btn btn-danger" role="button"><i class="fa fa-trash-o"></i></a>-->
				</td>
			</tr>
		@empty
			<tr><td colspan="42" class="text-center">
					<br/>
					@if($search)
						<div class="alert alert-info"><b>No lists matching "{{$search}}"</b></div>
					@else
						<div class="alert alert-info"><b>No lists yet!</b></div>
					@endif
				</td></tr>
		@endforelse
	</table>
		</div>
		<br>{{ $lists->links() }}
	@endif
	</div>
	<script>
		$(function(){
			$('body').on('click', '.list-delete-btn', function(){
				var listId = $(this).data('listId');
				dialogs.confirm("Are you sure to delete the list?", function(confirm){
					if(confirm){
						$.post('{{route('adminDeleteList')}}', {
							listId : listId
						}).success(function (res) {
							if (res.success) {
								dialogs.success('List Deleted', function () {
									window.location.href = '{{route('adminViewLists')}}';
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