
function renderConfigForm() {
	vent.trigger('configFormForm:beforeRender');
	var formOptions = getFormViewOptions(listConfigSchema
		, {
		formOptions: {
            "listImageTextFont": {
                "append" : '<br /><a href="#" class="preview-sample-og-btn"></a>'
            },
            "listImageTextFontSize": {
                "append" : '<a href="#" class="preview-sample-og-btn"></a>'
            },
            "listApprovedNotificationMailSubjectTemplate": {
                "append" : '<a class="show-approved-notification-reference" ><i class="fa fa-question-circle"></i> Variables reference</a>'
            },
            "listApprovedNotificationMailTemplate": {
                "append" : '<a class="show-approved-notification-reference" ><i class="fa fa-question-circle"></i> Variables reference</a>'
            },
            "listEditNotificationMailSubjectTemplate": {
                "append" : '<a class="show-edit-list-notification-reference" ><i class="fa fa-question-circle"></i> Variables reference</a>'
            },
            "listEditNotificationMailTemplate": {
                "append" : '<a class="show-edit-list-notification-reference" ><i class="fa fa-question-circle"></i> Variables reference</a>'
            }
		},
		events: {
			results: {
				onChange: function(e, node){

				},
				onInsert: function(e, node){

				}
			}
		}
	});

	$('#configFormContainer').find('#configForm, .form-results-box').html('');
	$('#configForm').jsonForm({
		schema: 
			listConfigSchema
		,
		form: formOptions,
		value: listConfigData,
		onSubmit: function (errors, values) {
		  if (errors) {
			$('#configFormResult').html('<p>I beg your pardon?</p>');
		  }
		  else {
			  listConfigData = values;
			  vent.trigger('hideForm', 'configForm');
		  }
			vent.trigger('config-form-submitted');
		}
	});
}

renderConfigForm();

(function() {
    $('.preview-sample-og-btn').html('<b>Preview a sample image to test this configuration</b>').click(function() {
        dialogs.confirm('Make sure that you have saved the config before previewing. Previews are generated with the config that is already saved!', function(res){
            if(!res) {
                return;
            }
            dialogs.hideAll();
            dialogs.alert('<img style="max-width: 100%;" src="' + previewOgImageUrl + '?v=' + (new Date()).getTime() + '">');
        })
    });
})();