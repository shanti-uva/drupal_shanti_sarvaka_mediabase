(function ($) { // jQuery wrapper function
	
	function inMBframe() {
	    try {
	        return window.self !== window.top;
	    } catch (e) {
	        return true;
	    }
	}
	
	/** Check to see if cookie is set to be in iframe and whether this matches current state, if not reload outside of iframe **/
	Drupal.behaviors.sarvaka_mb_iframe_links = {
		attach: function (context, settings) {
			if(context == window.document) {
				if(inMBframe()) {
					$('body').addClass('in-frame');
				} else {
					$('body').removeClass('in-frame');
				}
			}
		}
	};
}(jQuery));


