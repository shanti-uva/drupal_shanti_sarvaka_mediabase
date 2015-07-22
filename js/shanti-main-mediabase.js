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
					$('.avpbcoredesc .altlang').removeClass('altlang');
					if ($('#pb-core-desc-readmore a').eq(0).text().indexOf('More') > -1) {
						console.log('Read more link text: ' + $('#pb-core-desc-readmore a').eq(0).text());
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
				
	// Moved from Shanti Sarvaka shanti-main.js
	
	Drupal.behaviors.shantiSarvakaMbTrimDesc = {
	  attach: function (context, settings) {
	  	if (context == document) {
		  	// Pb core description trimming
				if($('.field-name-field-pbcore-description .field-item').length > 1) {
					var items = $('.field-name-field-pbcore-description > .field-items > .field-item');
					var multip = false;
					if (items.eq(0).find('p').length > 0) {
						multip = true;
						items.eq(0).find('p').eq(0).nextAll().each(function() { $(this).hide(); });
					}
					var ct = 0;
					items.each(function() {
						if ($(this).find('.content > .hidden').filter(":not(.altlang)").length > 0) { ct++; }
					});
					if(ct > 0 || multip) {
						//items.first().nextAll().hide();
						items.last().after('<p id="pb-core-desc-readmore" class="show-more"><a href="#">' + Drupal.t('Show More') + '</a></p>');
						if(!$(".avdesc").hasClass("show-more-height")) { $(".avdesc").addClass("show-more-height"); }
						$(".show-more > a").click(function (e) {
							var items = $('.field-name-field-pbcore-description > .field-items > .field-item');
							//items.first().nextAll('.field-item').slideToggle();
							items.eq(0).nextAll().find('.content > div').filter(":not(.altlang)").toggleClass('hidden');
							items.eq(0).find('p').eq(0).nextAll().toggle();
							//console.log($(".avdesc").attr('class'));
					     if($(".avdesc").hasClass("show-more-height")) {
					         $(this).text(Drupal.t('Show Less'));
					     } else {
					         $(this).text(Drupal.t('Show More'));
					     }
					     $(".avdesc").toggleClass("show-more-height");
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
	
} (jQuery)); // End of JQuery Wrapper
