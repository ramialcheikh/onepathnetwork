@if(!empty($users))
    @if(Session::has('error'))
        <div class="alert alert-danger">
            <b>Error: </b> {{Session::get('error')}}
        </div>
    @endif
    @if(Session::has('status'))
        <div class="alert alert-success">
            {{Session::get('status')}}
        </div>
    @endif
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-users"></i> Users (<b>{{$users->getTotal()}}</b>)</h3>
		</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table table-bordered">
                    <tr>
                        <td colspan="42" class="active">
                            {{Form::open([
                                "url" => route('adminUsersHome'),
                                "class" =>  'form-inline',
                                "method"    =>  'get'
                            ], $filters)}}
                            {{Form::text('filters[query]', @$filters['query'], ['class' => 'form-control', 'placeholder'   =>  'Search lists'])}}
                            <label>	{{ Form::checkbox('filters[autoApproveOnly]', @$filters['autoApproveOnly'], !empty($filters['autoApproveOnly'])) }}	Auto-Approved only</label>
                            {{ Form::submit('Search', ["class"  =>  'btn btn-default btn-sm']) }}
                            {{Form::close()}}
                        </td>
                    </tr>
                    <tr class="">
                        <th>Sl no:</th>
                        <th style="width: 50px;">Photo</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Auto approve enabled</th>
                        <th>
                            <a @if($sort === 'created_at') class="text-danger" @endif href="{{ Helpers::getUrlWithQuery(array('sort' => 'created_at', 'sortType' => ($sortType == 'asc') ? 'desc' : 'asc')) }}">Signed up on @if($sort === 'created_at')

                            @if($sortType === 'asc')
                            <i class="fa fa-caret-up">
                            @else
                            <i class="fa fa-caret-down">
                            @endif
                            </i>
                            @endif
                            </a>
                        </th>
                    </tr>
                @forelse($users as $key => $user)
                    <tr>
                        <td>{{$user->slNo}}</td>
                        <td><a target="_blank" href="{{UserHelpers::userProfileUrl($user)}}"><img src="{{UserHelpers::getSquareProfilePic($user)}}" alt="" width="50"></a></td>
                        <td>
                            <a target="_blank" href="{{UserHelpers::userProfileUrl($user)}}">{{$user->name}}</a>
                        </td>
                        <td>{{$user->email}}</td>
                        <td>
                            <div class="auto-approve-section">
                                @if($user->autoApproveEnabled)
                                    <span class="label label-success pull-left" style="margin-right: 5px;">Yes</span>
                                    <form class="form-inline pull-left" action="{{action('AdminUsersController@postDisableAutoApprove')}}" method="post">
                                        <input type="hidden" name="redirect" value=""/>
                                        <input type="hidden" name="userId" value="{{$user->id}}"/>
                                        <input type="submit" class="btn btn-danger btn-xs" value="Disable" />
                                    </form>
                                @else
                                    <span class="label label-default pull-left" style="margin-right: 5px;">Yes</span>
                                    <form class="form-inline pull-left" action="{{action('AdminUsersController@postEnableAutoApprove')}}" method="post">
                                        <input type="hidden" name="redirect" value=""/>
                                        <input type="hidden" name="userId" value="{{$user->id}}"/>
                                        <input type="submit" class="btn btn-default btn-xs" value="Enable" />
                                    </form>
                                @endif
                            </div>
                        </td>
                        <td>{{ Helpers::prettyTime($user->created_at, false)}}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-danger"><b>No Users found</b></td></tr>
                @endforelse

			</table>

                <script>
                    $('.auto-approve-section [name="redirect"]').each(function() {
                        $(this).val(window.location.href);
                    })
                </script>
			</div>
		</div>
	</div>
{{ $users->links()}}
@endif