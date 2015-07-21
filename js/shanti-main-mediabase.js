(function ($) { // jQuery wrapper function

	// Move the dom-id class so that the view block reloads all content and so does not duplicate filters, pager, etc.
	Drupal.behaviors.shanti_sarvaka_mb_bef_mainpage = {
		attach: function (context, settings) {
			domid = $('.shanti-view-dom-id').attr('data-dom-id');
			$("div.view-dom-id-" + domid).removeClass("view-dom-id-" + domid);
			$('.shanti-view-dom-id').addClass("view-dom-id-" + domid);
		}
	};
	

	// Various Markup changes for styling MB in sarvaka theme
	Drupal.behaviors.shantiSarvakaMbMarkupTweaks = {
		attach: function (context, settings) {
			if(context == window.document) {
				$('#edit-group-audience .form-item-group-audience-und', context).once('aveditcollaud').wrapInner('<div class="collection-details-audience"></div>');
				$('.collection-details-audience').once('aveditcollaud2').before($('.collection-details-audience > label').detach());
				$('#edit-group-audience .form-item-group-audience-und > label, #edit-field-subcollection > label').once('aveditsubcoll').prepend('<span class="icon shanticon-create"></span> ');
				$('#edit-field-characteristic > label').once('aveditsubjects').prepend('<span class="icon shanticon-subjects"></span> ');
				$('#edit-field-pbcore-coverage-spatial > label').once('aveditspatial').prepend('<span class="icon shanticon-places"></span> ');
				// Show more language descriptions
				if( $('.avpbcoredesc .field-item .content > .hidden').length > 0) {
					$('.showdesclang').removeClass('hidden');
				}
				$('.showdesclang a').click(function(e) {
					e.preventDefault();
					$('.avpbcoredesc .hidden').removeClass('hidden');
					$('.showdesclang').addClass('hidden');
					if ($('#pb-core-desc-readmore a').eq(0).text().indexOf('More') > -1) {
						$('#pb-core-desc-readmore a').eq(0).click();
					}
				});
			}
	  }
	};
	
     	
	Drupal.behaviors.shantiSarvakaMbSearchFlyoutCancel = {
			attach: function (context, settings) {
				if(context == window.document) {
													
				  var mbsrch = $(".search-group .form-control");  // the main search input
			    $(mbsrch).data("holder", $(mbsrch).attr("placeholder"));
			
			    // --- focusin - focusout
			    $(mbsrch).focusin(function () {
			        $(mbsrch).attr("placeholder", "");
			        $("button.searchreset").show("fast");
			    });
			    $(mbsrch).focusout(function () {
			        $(mbsrch).attr("placeholder", $(mbsrch).data("holder"));
			        $("button.searchreset").hide();
			
			        var str = "Enter Search...";
			        var txt = $(mbsrch).val();
			
			        if (str.indexOf(txt) > -1) {
			            $("button.searchreset").hide();
			            return true;
			        } else {
			            $("button.searchreset").show(100);
			            return false;
			        }
			    });
				
				}
		  }
		};	 
		
	Drupal.behaviors.shantiSarvakaMbSearchFlyoutCancel = {
			attach: function (context, settings) {
				if(context == window.document) {		

						if ($('.tabs.primary > .active:contains("My Media")').length ) { 							
							  $('body').addClass('page-my-media');							
						}
						if ($('.tabs.primary > .active:contains("My Collections")').length ) { 							
							  $('body').addClass('page-my-collections');							
						}
						if ($('.tabs.primary > .active:contains("My Workflow")').length ) { 							
							  $('body').addClass('page-my-workflow');							
						}
						if ($('.tabs.primary > .active:contains("My Memberships")').length ) { 							
							  $('body').addClass('page-my-memberships');							
						}												
				}
		 }
	};	
				
	
} (jQuery)); // End of JQuery Wrapper
