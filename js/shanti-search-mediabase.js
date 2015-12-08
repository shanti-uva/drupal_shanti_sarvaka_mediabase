(function ($) { // jQuery wrapper function

	// Add Placeholder text ("Enter Search") to Views search input
	Drupal.behaviors.shanti_sarvaka_mb_search_input_update = {
		attach: function (context, settings) {
			if (context == document) {
				console.log('here');
				$("div.form-type-textfield input.form-text").each(function() {
					console.log($(this).attr('placeholder'));
					if ($(this).attr('placeholder') == "") {
						$(this).attr('placeholder', 'Enter Search');
					}
				});
			}
		}
	};
	

} (jQuery)); // End of JQuery Wrapper
