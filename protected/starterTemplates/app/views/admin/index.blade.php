@extends('admin/layout')

@section('belowNavbar')
    <div class="container-fluid" style="background: #262730;">
        <div class="row">
            <div class="col-md-12" style="padding-top: 10px;padding-bottom: 10px;">
                <h4>
                    <a href="{{Config::get('appMeta.scriptHomepageLink')}}" target="_blank" style="color: #ffffff;">{{Config::get('appMeta.name')}} <small style="color: inherit;">v</small>{{Config::get('appMeta.version')}}</a>
                    &nbsp;
                    <div class="btn-group" role="group" aria-label="...">
                        <button type="button" class="btn btn-success btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                            Help
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="scriptHelpDropdown">
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="{{Config::get('appMeta.documentationLink')}}" target="_blank">Documentation</a></li>
                            <li role="presentation"><a role="menuitem" tabindex="-1" href="{{Config::get('appMeta.supportLink')}}" target="_blank">Support</a></li>
                        </ul>
                    </div>
                </h4>
                <div class="update-checker"></div>
                @if(!empty($installFileExistsError))
                    <br/>
                    <div class="alert alert-danger">
                        <b>Important:</b> {{$installFileExistsError}}
                    </div>
                @endif

            </div>

            <script>
                (function(){
                    var objectName = 'update-check-widget';
                    window[objectName] = {
                        showUpdateCheck: function (container) {
                            var containerId = 'updateChecker-' + (new Date()).getTime();
                            container.setAttribute('id', containerId);
                            container.innerHTML = '<iframe style="width:100%; height: 1.5em;" src="{{Config::get('appMeta.updateCheckUrl')}}?my-version={{Config::get('appMeta.version')}}&elm=' + encodeURIComponent('#' + containerId) +  '" frameborder="0"></iframe>';
                            window.addEventListener('message', function(e){
                                var data = e.data;
                                try{
                                    data = JSON.parse(data);
                                    if(data.type == "iframe-height-change") {
                                        var containerElement = document.querySelector(data.element);
                                        var iframe = containerElement.children[0];
                                        iframe.style.height = parseInt(data.height) + "px";
                                    }
                                } catch (ex){
                                    console.log(ex.message);
                                }
                            });
                        }
                    };
                    window[objectName].showUpdateCheck($('.update-checker')[0]);
                })();
            </script>

        </div>
    </div>
@stop

@section('content')
	<!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                            Dashboard
                        </h1>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Today's stats</h3>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
										<a href="{{route('adminView@@plural-pascalCase@@')}}" style="display:block;" class="panel panel-primary">
											<div class="panel-heading">
												<div class="row">
													<div class="col-xs-3">
														<i class="fa fa-pencil-square-o fa-3x"></i>
													</div>
													<div class="col-xs-9 text-right">
														<div class="huge">{{@$todayStats['@@singular-pascalCase@@']}}</div>
														<div>New @@plural-pascalCase@@</div>
													</div>
												</div>
											</div>
										</a>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
										<a href="{{route('adminUsersHome')}}" style="display:block;" class="panel panel-green">
											<div class="panel-heading">
												<div class="row">
													<div class="col-xs-3">
														<i class="fa fa-user fa-3x"></i>
													</div>
													<div class="col-xs-9 text-right">
														<div class="huge">{{$todayStats['User']}}</div>
														<div>New Users</div>
													</div>
												</div>
											</div>
										</a>
									</div>
								</div>
							</div>
						</div>

						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Overall stats</h3>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
										<a href="{{route('adminView@@plural-pascalCase@@')}}" style="display:block;" class="panel panel-primary">
											<div class="panel-heading">
												<div class="row">
													<div class="col-xs-3">
														<i class="fa fa-pencil-square-o fa-3x"></i>
													</div>
													<div class="col-xs-9 text-right">
														<div class="huge">{{@$overallStats['@@singular-pascalCase@@']}}</div>
														<div>@@plural-pascalCase@@</div>
													</div>
												</div>
											</div>
										</a>
									</div>
									<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
										<a href="{{route('adminUsersHome')}}" style="display:block;" class="panel panel-green">
											<div class="panel-heading">
												<div class="row">
													<div class="col-xs-3">
														<i class="fa fa-user fa-3x"></i>
													</div>
													<div class="col-xs-9 text-right">
														<div class="huge">{{$overallStats['User']}}</div>
														<div>Users</div>
													</div>
												</div>
											</div>
										</a>
									</div>
								</div>
							</div>
						</div>
                    </div>
                </div>
                <!-- /.row -->
	<div class="row">
		<div class="col-md-12 col-lg-6">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4><i class="fa fa-users"></i> New users in the last 30 days</h4>
				</div>
				<div class="panel-body">
					<div id="newUsersChart" style="height: 200px;"></div>
				</div>
			</div>
		</div>
	</div>

	<script>
		$(function(){
			new Morris.Line({
				// ID of the element in which to draw the chart.
				element: 'newUsersChart',
				// Chart data records -- each entry in this array corresponds to a point on
				// the chart.
				data: {{$last30DaysUserRegistrations}},
				// The name of the data record attribute that contains x-values.
				xkey: 'date',
				// A list of names of data record attributes that contain y-values.
				ykeys: ['stats'],
				// Labels for the ykeys -- will be displayed when you hover over the
				// chart.
				labels: ['Registrations'],
				resize: true
			});
		})
	</script>
@stop