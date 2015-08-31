@extends('admin/layout')

@section('content')
	<!-- Page Heading -->
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				@@singular-pascalCase@@ Config
			</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">

		</div>
	</div>
	<!-- /.row -->
<script>
	var @@singular@@ConfigSchema = {{$@@singular@@ConfigSchema or 'null'}};	
	var @@singular@@ConfigData = {{$@@singular@@ConfigData or 'null'}};	
</script>
<div class="row">
	<div class="col-md-10">
		<div class="panel panel-info">
			<div class="panel-heading">@@singular-pascalCase@@ Configuration</div>
			<div class="panel-body">
				<div class="" id="configFormContainer">
					<form class="@@singular@@-form-common" action="" id="configForm"></form>
					<div class="form-results-box" id="configFormResult"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="{{asset('js/admin/admin.js')}}"></script>

<script>
	vent.on('config-form-submitted', function(){
		$.post('{{ route('adminConfig@@singular-pascalCase@@')}}', {
			@@singular@@Config: @@singular@@ConfigData
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

<script src="{{asset('js/admin/@@singular@@Config.js')}}"></script>

@stop