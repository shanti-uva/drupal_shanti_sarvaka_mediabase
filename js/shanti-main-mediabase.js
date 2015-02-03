(function ($) { // jQuery wrapper function

	// Move the dom-id class so that the view block reloads all content and so does not duplicate filters, pager, etc.
	Drupal.behaviors.shanti_sarvaka_mb_bef_mainpage = {
		attach: function (context, settings) {
			domid = $('.shanti-view-dom-id').attr('data-dom-id');
			$("div.view-dom-id-" + domid).removeClass("view-dom-id-" + domid);
			$('.shanti-view-dom-id').addClass("view-dom-id-" + domid);
		}
	};

	Drupal.behaviors.shantiSarvakaMbTrimDesc = {
	  attach: function (context, settings) {
	  	// Pb core description trimming
			if($('.field-name-field-pbcore-description .field-item').length > 1) {
				var items = $('.field-name-field-pbcore-description > .field-items > .field-item');
				if(items.length > 1 ) {
					items.first().nextAll().hide();
					items.last().after('<p id="pb-core-desc-readmore" class="show-more"><a href="#">' + Drupal.t('Show More') + '</a></p>');
					if(!$(".avdesc").hasClass("show-more-height")) { $(".avdesc").addClass("show-more-height"); }
					$(".show-more > a").click(function (e) {
						var items = $('.field-name-field-pbcore-description > .field-items > .field-item');
						items.first().nextAll('.field-item').slideToggle();
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
	};

	// Various Markup changes for styling MB in sarvaka theme
	Drupal.behaviors.shantiSarvakaMbMarkupTweaks = {
		attach: function (context, settings) {
			if(context == window.document) {
				$('#edit-group-audience .form-item-group-audience-und').wrapInner('<div class="collection-details-audience"></div>');
				$('.collection-details-audience').before($('.collection-details-audience > label').detach());
				$('#edit-group-audience .form-item-group-audience-und > label, #edit-field-subcollection > label').prepend('<span class="icon shanticon-create"></span> ');
				$('#edit-field-characteristic > label').prepend('<span class="icon shanticon-subjects"></span> ');
				$('#edit-field-pbcore-coverage-spatial > label').prepend('<span class="icon shanticon-places"></span> ');
			}
	  }
	};
	
	// Applies wookmark js to related videos tab div by calling Drupal behaviors
	Drupal.behaviors.shantiSarvakaMbRelatedTab = {
		attach: function (context, settings) {
			if(context == window.document) {
				$('a#related-tab').on('shown.bs.tab', function(e) {
					Drupal.attachBehaviors('#related');
				});
			}
	  }
	};
      
	Drupal.behaviors.shantiSarvakaMbTranscriptSearchToggle = {
		attach: function (context, settings) {
			if(context == window.document) {
				$('.searchtrans').click( function(){  
					$('.transcript-search-wrapper').slideToggle();		
					// $('.transcript-container').toggleClass('show-search');	
				});
			}
	  }
	};


	Drupal.behaviors.shantiSarvakaMbTranscriptSearchCancel = {
		attach: function (context, settings) {
			if(context == window.document) {
												
			  var mbsrch = $(".input-group > .form-control");  the main search input
		    $(mbsrch).data("holder", $(mbsrch).attr("placeholder"));
		
		     --- features inputs - focusin / focusout
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
		            $(".searchreset").hide();
		            return true;
		        } else {
		            $(".searchreset").show(100);
		            return false;
		        }
		    });
			
			}
	  }
	};	 
	
	
	Drupal.behaviors.shantiSarvakaMbTranscriptLanguageDropdownIcon = {
		attach: function (context, settings) {
			if(context == window.document) {
				$('.tier-selector .filter-option').replaceWith('<span class="fa fa-comments-o"></span>');
			}
	  }
	};	
     	

} (jQuery)); // End of JQuery Wrapper
