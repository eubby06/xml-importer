(function($) {

	//short for Estatik XML Importer :)
	var EXI = {

		loadingIndicator : '<img width="20" src="'+PLUGIN_URL+'/js/spinner.gif" />',
		xml : '',
		sourceAttributeContainer : $('.source-attributes-container'),
		mappingDropdownsTemplate : '',
		importButton : $('.exi-import-button'),
		
		init		: function() {
					//console.log('initializing exi app js');
					$('.exi-validate-xml').on('click', EXI.validateXML);
					$('form#exi-attributes').on('submit', EXI.submitForm);

		},

		submitForm		: function(e) {

						e.preventDefault();

						var importResultContainer = $('.exi-import-result');

						var formData = $(this).serialize();

						$.ajax({
							url: estatik_ajax.ajaxurl,
							data: {
								'action': 'new_import',
								'data': {action: 'submit_form', xml: EXI.xml, xpath: EXI.xpath, data: formData}
							},
							success: function(response) {
									if(response) {
										importResultContainer.hide();
										var message = ' records uploaded.';
										importResultContainer.html(response + message);
									}
							},
							beforeSend: function() {
								var message = 'Importing now... Please do not close the browser.';
								importResultContainer.append(EXI.loadingIndicator + message);
							}

						});
		},

		validateXML 	: function(e) {

						e.preventDefault();

						EXI.xml = $('input.exi-url').val();
						var resultDisplayer = $('.exi-result');
						

						$.ajax({
							url: estatik_ajax.ajaxurl,
							data: {
								'action': 'new_import',
								'data': {action: 'validate_xml', xml: EXI.xml}
							},
							success: function(response) {

									if(response == 1) {
										resultDisplayer.text('This xml is valid.');
										EXI.getXPaths();
									} else {
										resultDisplayer.text('There is no record found.');
									}
							},
							beforeSend: function() {
								resultDisplayer.html( EXI.loadingIndicator + 'Validating source...');
							}

						});
		},

		getXPaths 	: function() {

						var xpathsLoadingDisplayer = $('.exi-loading-xpaths');

						$.ajax({
							url: estatik_ajax.ajaxurl,
							data: {
								'action': 'new_import',
								'data': {action: 'get_xpaths', xml: EXI.xml}
							},
							success: function(response) {

									var options = '';

									$.each(response, function(key, val) {
										options += '<option value="' + val + '">' + val + '</option>';
									});

									var select = '<label>Select XPath</label>' +
													'<select class="exi-xpath">'
													  + options +
													'</select>';

									$('.exi-xpaths-dropdown').html(select);

									//add change event
									$('.exi-xpath').on('change', EXI.loadAttributes);

							},
							beforeSend: function() {
								xpathsLoadingDisplayer.html( EXI.loadingIndicator + 'Loading xpaths here...');
							}
						});
		},

		loadAttributes	: function() {
			//this is the selected xpath
			var xpath = $(this).val();

			//find attributes based on this xpath
			EXI.getAttributes(xpath);

			//set it global
			EXI.xpath = xpath;
		},

		getAttributes 	: function(xpath) {

						$.ajax({
							url: estatik_ajax.ajaxurl,
							data: {
								'action': 'new_import',
								'data': {action: 'get_attributes', xpath: xpath, xml: EXI.xml}
							},
							success: function(response) {
						console.log(response);
								//for source -----------------------------------------
								var sourceOptions = '<option>Select Attribute</option>';

								$.each(response.source, function(key, val) {
									sourceOptions += '<option value="' + val + '">' + val + '</option>';
								});

								var sourceSelect = '<select class="exi-value">'
												  + sourceOptions +
												'</select>';


								//for allowed attributes ----------------------------
								var allowedOptions = '<option>Select Field</option>';

								$.each(response.allowed, function(key, val) {
									allowedOptions += '<option value="' + val + '">' + val + '</option>';
								});

								var allowedSelect = '<select class="exi-key">'
												  + allowedOptions +
												'</select>';

								//we need to put it in a template var and then ...				
								EXI.mappingDropdownsTemplate = allowedSelect + sourceSelect;

								//append the template to the container
								EXI.sourceAttributeContainer.html('');
								EXI.sourceAttributeContainer.append(EXI.mappingDropdownsTemplate);

								$('.exi-key').on('change', EXI.addSelectClassName);
								$('.exi-add-attribute').on('click', EXI.addAttribute);

							},
							beforeSend: function() {
								EXI.sourceAttributeContainer.html(EXI.loadingIndicator + 'Loading attributes here...');
							}
						});
		},

		addSelectClassName : function() {
			//get the selected field
			var value = $(this).val();

			if(value == 'photos') {
				var nameArray = value + '[]';
				//set the name of the select tag based on the selected field
				$(this).next().attr('name', nameArray);
				$(this).next().attr('multiple', true);
			}
			else {
				//set multiple attribute for photos
				$(this).next().attr('name', value);
				$(this).next().attr('multiple', false);
			}
		},

		addAttribute : function(e) {

			e.preventDefault();

			EXI.sourceAttributeContainer.append(EXI.mappingDropdownsTemplate);
			$('.exi-key').on('change', EXI.addSelectClassName);

		}
	}

	EXI.init();

})(jQuery);