@extends('admin/layout')

@section('content')
    <style>
        .jsonform-error-listImageTextFont .img-thumbnail {
            display: none;
        }
    </style>

    <script>
        var previewOgImageUrl = '{{route('adminPreviewOgImage')}}';
    </script>

	<!-- Page Heading -->
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">
				List Config
			</h1>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">

		</div>
	</div>
	<!-- /.row -->
<script>
	var listConfigSchema = {{$listConfigSchema or 'null'}};	
	var listConfigData = {{$listConfigData or 'null'}};	
</script>
<div class="row">
	<div class="col-md-10">
		<div class="panel panel-info">
			<div class="panel-heading">List Configuration</div>
			<div class="panel-body">
				<div class="" id="configFormContainer">
					<form class="list-form-common" action="" id="configForm"></form>
					<div class="form-results-box" id="configFormResult"></div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="{{asset('js/admin/admin.js')}}"></script>

<script>
	vent.on('config-form-submitted', function(){
		$.post('{{ route('adminConfigList')}}', {
			listConfig: listConfigData
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

    <div class="modal fade" id="approvedNotificationReferenceModal">
        <div class="modal-dialog modal-lg" style="max-width: 600px;">
    		<div class="modal-content">
    			<div class="modal-header">
    				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    				<h4 class="modal-title">List approved notifications - variables reference</h4>
    			</div>
    			<div class="modal-body">
                    <h4>These variables when used(with the enclosing square brackets) will be replaced with the corresponding data(mentioned in the variable descriptions below.)</h4><br/>
                    <ol>
                        <li>
                            <h4>[RecipientName]</h4>
                            <p>The recipient's name</p>
                        </li>
                        <li>
                            <h4>[ListTitle]</h4>
                            <p>The title of the list</p>
                        </li>
                        <li>
                            <h4>[ListUrl]</h4>
                            <p>The url of the list</p>
                        </li>
                        <li>
                            <h4>[ListLink]</h4>
                            <p>The HTML link of the list (Its a link tag ("a" tag) with the list's title as the anchor text.). This should not be confused as the link's url</p>
                        </li>
                    </ol>
    			</div>
    			<div class="modal-footer">
    				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    			</div>
    		</div><!-- /.modal-content -->
    	</div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="listEditedNotificationReferenceModal">
        <div class="modal-dialog modal-lg" style="max-width: 600px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">List edited notifications - variables reference</h4>
                </div>
                <div class="modal-body">
                    <h4>These variables when used(with the enclosing square brackets) will be replaced with the corresponding data(mentioned in the variable descriptions below.)</h4><br/>
                    <ol>
                        <li>
                            <h4>[RecipientName]</h4>
                            <p>The recipient's name</p>
                        </li>
                        <li>
                            <h4>[NewListLink]</h4>
                            <p>The HTML link of the <b>new list</b> (Its a link tag ("a" tag) with the list's title as the anchor text.). This should not be confused as the link's url</p>
                        </li>
                        <li>
                            <h4>[OriginalListTitle]</h4>
                            <p>The original list's title</p>
                        </li>
                        <li>
                            <h4>[OriginalListLink]</h4>
                            <p>The HTML link of the <b>original list</b> (Its a link tag ("a" tag) with the list's title as the anchor text.). This should not be confused as the link's url</p>
                        </li>
                        <li>
                            <h4>[NewListCreatorName]</h4>
                            <p>The name of the user who edited the recipient's list</p>
                        </li>
                    </ol>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <script>
        $('body').on('click', '.show-approved-notification-reference', function(e) {
            $('#approvedNotificationReferenceModal').modal('show');
            e.preventDefault();
        });
        $('body').on('click', '.show-edit-list-notification-reference', function() {
            $('#listEditedNotificationReferenceModal').modal('show');
            e.preventDefault();
        });
    </script>
<script src="{{asset('js/admin/listConfig.js')}}"></script>

@stop