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
                                $('#edit-field-transcript .form-item-field-transcript-und-0 > label').once('avedittrans').prepend('<span class="fa fa-comments-o"></span> ');
				$('#edit-field-characteristic > label').once('aveditsubjects').prepend('<span class="icon shanticon-subjects"></span> ');
				$('#edit-field-pbcore-coverage-spatial > label').once('aveditspatial').prepend('<span class="icon shanticon-places"></span> ');
				// Show more language descriptions
				if( $('.avpbcoredesc .field-item .content > .hidden').length > 0) {
					$('.showdesclang').removeClass('hidden');
				}
				// Show All Languages link: Shows everything and removes any toggles. Can't hide after this.
				$('.showdesclang a').click(function(e) {
					e.preventDefault();
					$('#pb-core-desc-readmore').show(); // Show the Read More link
					$('.showdesclang').addClass('hidden'); // Hide the Show All Languages link
					// if more than one altlang field show everything and change to show less (This doesn't work?)
					if ($('.avpbcoredesc .altlang').length > 1) {
						$('.avpbcoredesc .hidden').removeClass('hidden');
						$('#pb-core-desc-readmore a').eq(0).text('Show Less');
					}
					// Default just show altlang (Show everything and remove toggle)
					$('.avpbcoredesc .altlang').removeClass('hidden altlang'); // Show all alternate languages
                    $('.pb-desc-label').removeClass('hidden'); // Show labels for alternate languages
					$('.avpbcoredesc .field-name-field-description .field-item > *').show(); // Show all paragraph elements in field description itesms
					$('.avpbcoredesc #pb-core-desc-readmore').hide(); // Hide the Readmore link
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
	  	    // Scoped Function to Show or Hide Description elements. Called below
	  	    function MbShowHideDesc(descElItems, show) {
	  	        if (typeof(show) == "undefined") { show = false; }
                var multip = false,
                           maxlen = 550,  // Set this for max character length to show by default
                           textlen = 0,
                           changeit = false,
                           heightlost = 0;
                if (descElItems.text().length > maxlen) {
                    descElItems.each(function() {
                        if ($(this).find('div.hidden.altlang').length > 0) { return; } // Skip alt lang divs for counting text length as they are hidden by default
                        $(this).find('p').eq(0).nextAll().each(function() { 
                            if ($(this).text().length == 0) { return; } // Don't process empty paragraphs
                            if (changeit && !show) { 
                                heightlost += $(this).height();
                                $(this).hide(300);
                            } else { $(this).show(300); }
                            textlen += $(this).text().length;
                            if (textlen > maxlen && textlen > $(this).text().length) { 
                                if (show) { $(this).show(); } else { $(this).hide();}
                                changeit = true;
                                multip = true;
                            }
                        });
                    });
                    if (heightlost > 0) { 
                        var st = $(window).scrollTop(400);
                        $(window).scrollTop(st - heightlost);
                    }
                }
                return multip;  // Return whether there are hidden paragraphs
	  	    }
		  	// Pb core description trimming for a/v nodes
				if($('.field-name-field-pbcore-description .field-item').length > 1) {
					var items = $('.field-name-field-pbcore-description > .field-items > .field-item'),
					       showing = false,
					       multip = false,
					       ct = 0;
					multip = MbShowHideDesc(items);
					// Determine if there any divs showing content
					items.each(function() { 
					    if (jQuery(this).find('div.content > div.hidden').length == 0) { showing = true; }
					    if ($(this).find('.content > .hidden').not(".altlang").length > 0) { ct++; }
				    });
					
					if(ct > 0 || multip == true) {
						//items.first().nextAll().hide();
						// Add Show More / Show Less Link
						items.last().after('<p id="pb-core-desc-readmore" class="show-more"><a href="#">' + Drupal.t('Show More') + '</a></p>');
						if (!showing) { $('#pb-core-desc-readmore').hide();} // Hide show more if no divs showing content
						if(!$(".avdesc").hasClass("show-more-height")) { $(".avdesc").addClass("show-more-height"); }
						$(".show-more > a").click(function (e) {
							var items = $('.field-name-field-pbcore-description > .field-items > .field-item');
							var showitems = ($(this).text() == Drupal.t('Show More')) ? true : false;
							if (showitems) {
							    MbShowHideDesc(items, true);
							    $(this).text(Drupal.t('Show Less'));
							} else {
                                MbShowHideDesc(items, false);
                                $(this).text(Drupal.t('Show More'));
							}
							 e.preventDefault();
						});
					}
				}
				
				// Description Trimming for collections
				if ($('.node-type-collection .field-type-text-with-summary, .node-type-subcollection .field-type-text-with-summary').length > 0) {
				    var maxlen = 500;
				    var descel = $('.node-type-collection .field-type-text-with-summary, .node-type-subcollection .field-type-text-with-summary').eq(0);
				    if (descel.text().length > maxlen) {
				        var totalchar = 0;
				        var eltotal = 0;
				        var ishidden = false;
				        $('.node-type-collection .field-type-text-with-summary .field-item, .node-type-subcollection .field-type-text-with-summary .field-item').each(function() {
				            $(this).children().each(function() {
				                totalchar += $(this).text().length;
				                eltotal++;
				                if (totalchar > maxlen && eltotal > 1) {
				                    $(this).addClass('overmax');
				                    $(this).hide();
				                    ishidden = true;
				                }
				            });
				        });
				        if (ishidden) {
				            $('.node-type-collection .field-type-text-with-summary, .node-type-subcollection .field-type-text-with-summary')
				                .append('<div class="coldesc-showmore show-more"><a class="link show" href="#">Show more</a></div>');
				            $('.coldesc-showmore a.link').click(function(e) {
				                if ($(this).hasClass('show')) {
				                    $('.overmax').show();
				                    $(this).text('Show less');
				                    $(this).removeClass('show');
				                } else {
				                    $('.overmax').hide();
                                    $(this).text('Show more');
                                    $(this).addClass('show');
				                }
                                e.preventDefault();
				            });
				        }
				    }
				}
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
							  pwidth = $('.kWidgetIframeContainer.kaltura-embed-processed').parent().width(),
							  height = ((ratio == '4:3') ? pwidth / 1.3333 : pwidth / 1.7778) + ((ratio == '4:3') ? 32 : 42), // add 32/42 px to height for control bar
							  divclass = (ratio == '4:3') ? 'ratio-4-3' : 'ratio-16-9';		
						$('.kWidgetIframeContainer.kaltura-embed-processed').addClass(divclass).css({
								'position':'', 
								'top':'', 
								'left': '', 
								'right':'', 
								'bottom':'', 
								'width':'100%',
								'height': height + 'px'
						});
					});
				});
			}
		}
	};
	
	/**
	 * Make sure gallery thumbnails from kaltura use the proper size. Hack because php code to save thumb url is not working properly 
	 * ndg, 2015-12-07
	 */
	Drupal.behaviors.shantiAVThumbnailFix = {
		attach: function(context, settings) {
			if (context == document) { 
				$('li.shanti-thumbnail.video img').each(function() { 
					var src = $(this).attr('src'); 
					if (src.indexOf('kaltura.com') > -1 && src.indexOf('/version') > -1) { 
						var pts = src.split('version'); $(this).attr('src', pts[0] + '/width/240/height/220/type/1'); 
					} 
				});
			}
		}
	};

	/**
	 * Set full-height background white on sidebars
	 */
	Drupal.behaviors.shantiAVEqualHeights = {
	  attach: function (context, settings) { 
	    if(context == window.document) {        

	        $(window).load(function() {
	        	function set_equal_heights() {
		          	$('.sidebar-second .equal-height').matchHeight({
		              target: $('main.main-wrapper')
		          	});
	            };
	            window.setTimeout( set_equal_heights, 1000 );
	        });

	        $(window).load(function() {
	        	function av_equal_heights() {
		          	$('.has-transcript .av-main-wrapper > div').matchHeight({
		              target: $('.av-main-video-section')
		          	});
	            };
	            window.setTimeout( av_equal_heights, 1000 );
	        });

/*	        $(window).load(function() {
		            $('.has-transcript .show-more a').click(function() {

						if($('.video-overview p').each().is(":visible")) {
			        		$('.av-main-wrapper > div').matchHeight({ 
			        			target: $('.av-main-video-section') 
			        		});
						} 

		            });
	        });
*/

/*
	        $( document ).ready(function() {
	          	$('.has-transcript .av-main-wrapper > div').matchHeight({
	              target: $('.av-main-video-section')
	          	}); 

				//	          	setTimeout("$('.has-transcript .av-main-wrapper > div').matchHeight({ target: $('.av-main-video-section') })", 1500);
	        });
	        

            $('.has-transcript .show-more a').click(function() {

				if($('.video-overview p').each().is(":visible")) {
	        		$('.av-main-wrapper > div').matchHeight({ 
	        			target: $('.av-main-video-section') 
	        		});
				} 
            }); 
*/
				//	          $(window).bind('load orientationchange resize', Drupal.behaviors.shantiAVEqualHeights );  


			/* avoid flash on load for the Star Ratings below Audio or Video players */
			$('.avrating').css('display','block');


	    }
	  } 
	};





} (jQuery)); // End of JQuery Wrapper
