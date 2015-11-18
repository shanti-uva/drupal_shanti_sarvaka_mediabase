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
					$('#pb-core-desc-readmore').show();
					$('.showdesclang').addClass('hidden');
					// if more than one altlang field show everything and change to show less
					if ($('.avpbcoredesc .altlang').length > 1) {
						$('.avpbcoredesc .hidden').removeClass('hidden');
						$('#pb-core-desc-readmore a').eq(0).text('Show Less');
					}
					// Default just show altlang
					$('.avpbcoredesc .altlang').removeClass('hidden altlang');
					
				});
			}
	  }
	};
 	 
		
	Drupal.behaviors.shantiSarvakaAccountTabs = {
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
				
	// Moved from Shanti Sarvaka shanti-main.js
	
	Drupal.behaviors.shantiSarvakaMbTrimDesc = {
	  attach: function (context, settings) {
	  	if (context == document) {
		  	// Pb core description trimming
				if($('.field-name-field-pbcore-description .field-item').length > 1) {
					var items = $('.field-name-field-pbcore-description > .field-items > .field-item');
					// Determine if there any divs showing content
					var showing = false;
					items.each(function() { if (jQuery(this).find('div.content > div.hidden').length == 0) { showing = true; }});
					var multip = false;
					if (items.eq(0).find('p').length > 1) {
						multip = true;
						items.eq(0).find('p').eq(0).nextAll().each(function() { $(this).hide(); });
					}
					var ct = 0;
					items.each(function() {
						if ($(this).find('.content > .hidden').not(".altlang").length > 0) { ct++; }
					});
					if(ct > 0 || multip == true) {
						//items.first().nextAll().hide();
						items.last().after('<p id="pb-core-desc-readmore" class="show-more"><a href="#">' + Drupal.t('Show More') + '</a></p>');
						if (!showing) { $('#pb-core-desc-readmore').hide();} // Hide show more if no divs showing content
						if(!$(".avdesc").hasClass("show-more-height")) { $(".avdesc").addClass("show-more-height"); }
						$(".show-more > a").click(function (e) {
							var items = $('.field-name-field-pbcore-description > .field-items > .field-item');
							//items.first().nextAll('.field-item').slideToggle();
							var divstotoggle = items.eq(0).nextAll().find('.content > div');
							if ($('.showdesclang').is(":visible")) {
								divstotoggle = divstotoggle.filter(":not(.altlang)");
							} 
					     if($(this).text() == Drupal.t('Show More')) {
					     	// When Show More is clicked
					         $(this).text(Drupal.t('Show Less'));
					         divstotoggle.removeClass('hidden');
									 items.eq(0).find('p').eq(0).nextAll().show();
					     		$(".avdesc").addClass("show-more-height");
					     } else {
					     	// When Show Less is clicked
					         $(this).text(Drupal.t('Show More'));
					         divstotoggle.addClass('hidden');
									 items.eq(0).find('p').eq(0).nextAll().hide();
					     		$(".avdesc").removeClass("show-more-height");
					     }
							 e.preventDefault();
						});
					}
				}
	
				// Description Trimming
				/* This makes there be multiple "Show More"s on Dreams page
					Could perhaps use } else { if needed for other situations
	
				$('.description.trim').each(function() {
				 	if($(this).text().length > 1000 && $(this).find('p').length > 1 && $(this).find('div.show-more').length == 0) {
				 		var p1 = $(this).find('p').first();
				 		p1.siblings('p').hide();
				 		$(this).append('<div class="show-more"><a href="#">Show more</a></div>');
				 	}
				});
				$('.description.trim .show-more a').each(function() {
					$(this).click(function(event) {
						event.preventDefault();
						$(this).parent('.show-more').toggleClass('less');
						var parent = $(this).parents('.description.trim');
						var ps = parent.find('p').first().siblings('p');
						ps.slideToggle();
						var txt = $(this).text();
						txt = (txt.indexOf('more') > -1) ? 'Show less' : 'Show more';
						$(this).text(txt);
					});
				});
				*/
			}
		} // end context = document
	};



		// --- unhiding shanti-filters: inline styles keeps the default dropdown from flashing onLoad before the bootstrap-select script/css loads
	Drupal.behaviors.shantiFiltersOnLoadFlickerControl = {
	  	attach: function (context, settings) {
	  		$(".front .control-box-cell-filters").show( "fast" );
	  		if ($.trim($(".control-box-cell-header").html()) == '') {
	  			$(".control-box-cell-header").html('<span class="label">Recent Additions</span> (No matches)');
	  		}
	    }
	};

	

	Drupal.behaviors.shantiOpenAVLoginPanel = {
	  	attach: function (context, settings) {
	  		$("#accordionedit-drupal-login .panel-collapse").collapse('show');
	    }
	};
	
	Drupal.behaviors.shantiAVVideoFix = {
		attach: function(context, settings) {
			if (context == document) { 
				$('.kWidgetIframeContainer.kaltura-embed-processed').once('videosizeadjustment', function() {
					$('.kWidgetIframeContainer.kaltura-embed-processed').prev('div').remove();
					$('.kWidgetIframeContainer.kaltura-embed-processed iframe').on('load', function() { 
						var ratio = Drupal.settings.mediabase.vratio,
							  width = (ratio == '4:3') ? 520 : 667,
							  height = 425,
							  maxwidth = (ratio == '4:3') ? 550 : 720,
							  divclass = (ratio == '4:3') ? 'ratio-4-3' : 'ratio-16-9';
						$('.kWidgetIframeContainer.kaltura-embed-processed').addClass(divclass).css({
								'position':'', 
								'top':'', 
								'left': '', 
								'right':'', 
								'bottom':'', 
								'width': width + 'px', 
								'height': height+ 'px'
						});
						$('.kWidgetIframeContainer.kaltura-embed-processed').parent().css('max-width', maxwidth + 'px'); 
					});
				});
			}
		}
	};


} (jQuery)); // End of JQuery Wrapper
