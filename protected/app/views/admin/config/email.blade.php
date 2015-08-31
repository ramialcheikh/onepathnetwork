@extends('admin/layout')

@section('content')
	<!-- Page Heading -->
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				Email Config
			</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">

		</div>
	</div>
	<!-- /.row -->
<script>
	var emailConfigSchema = {{$emailConfigSchema or 'null'}};
	var emailConfigData = {{$emailConfigData or 'null'}};
</script>
<div class="row">
	<div class="col-md-10">
		<div class="panel panel-info">
			<div class="panel-heading">Email Configuration</div>
			<div class="panel-body">
				<div class="" id="configFormContainer">
					<form class="email-form-common" action="" id="configForm"></form>
					<div class="form-results-box" id="configFormResult"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="{{asset('js/admin/admin.js')}}"></script>

<script>
	vent.on('config-form-submitted', function(){
		$.post('{{ route('adminConfigEmail')}}', {
			emailConfig: emailConfigData
		}).success(function(res){
			if(res.success) {
				dialogs.success('Config Saved');
			} else if(res.error) {
				dialogs.error('Error occured\n' + res.error);
			} else {
				dialogs.error('Some Error occured');
			}
		}).fail(function(res){
			dialogs.error(res.responseText);
		});
	})
</script>

<script src="{{asset('js/admin/emailConfig.js')}}"></script>

@stop