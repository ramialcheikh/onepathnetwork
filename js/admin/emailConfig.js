
function renderConfigForm() {
	vent.trigger('configFormForm:beforeRender');
	var formOptions = getFormViewOptions(emailConfigSchema
		, {
		formOptions: {

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
			emailConfigSchema
		,
		form: formOptions,
		value: emailConfigData,
		onSubmit: function (errors, values) {
		  if (errors) {
			$('#configFormResult').html('<p>I beg your pardon?</p>');
		  }
		  else {
			  emailConfigData = values;
			  vent.trigger('hideForm', 'configForm');
		  }
			vent.trigger('config-form-submitted');
		}
	});
}

renderConfigForm();