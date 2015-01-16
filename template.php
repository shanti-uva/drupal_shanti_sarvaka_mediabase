<?php

/**
 * @file
 * template.php
 */
 
function sarvaka_mediabase_theme() {
  return array(
    'audio_node_form' => array(
      'render element' => 'form',
      'template' => 'av-node-form',
      'path' => drupal_get_path('theme', 'sarvaka_mediabase') . '/templates'
    ),
    'collection_node_form' => array(
      'render element' => 'form',
      'template' => 'av-node-form',
      'path' => drupal_get_path('theme', 'sarvaka_mediabase') . '/templates'
    ),
    'video_node_form' => array(
      'render element' => 'form',
      'template' => 'av-node-form',
      'path' => drupal_get_path('theme', 'sarvaka_mediabase') . '/templates'
    ),
  );
}

function sarvaka_mediabase_form_alter(&$form, &$form_state, $form_id) {
	if($form_id == "views_exposed_form") {
		//$form['#ajax']['wrapper'] = 'block-views-browse-media-home-block';
	}
}

function sarvaka_mediabase_preprocess_block(&$vars) {
	//dpm($vars, 'in pp block');
	if(!empty($vars['#facet']['label'])) {
		$vars['#facetlabel'] = $vars['#facet']['label'];
	}
}

function sarvaka_mediabase_preprocess_region(&$vars) {
	if($vars['region'] == 'search_flyout') {
		// For search flyout in mediabase, sniff out facet api blocks so that they can be placed in tabs
		$elements = $vars['elements'];
		//dpm($vars, 'vars in pp region');
		$children = element_children($elements);
		$facets_done = FALSE;
		$prefacetmu = $postfacetmu = '';
		$facetmu = '<div class="tab-content">';
		$facettabs = array();
		foreach($children as $ename) {
			if(strpos($ename, 'facetapi') > -1) {
				$facets_done = TRUE;
				list($flabel, $fname) = sarvaka_mediabase_get_facet_info($elements[$ename]['#block']->delta);
				$srflabel = strtolower($flabel);
				$facettabs[] = $flabel;
				$facetmu .= "<div class=\"facet-{$srflabel} tab-pane active\"><div class=\"kmaps-tree facet-{$srflabel} view-wrap\"><div class=\"shoppingcart\" display=\"none;\"></div>{$elements[$ename]['#children']}</div></div>";
			} elseif (!$facets_done) {
				$prefacetmu .= $elements[$ename]['#children'];
			} else {
				$facettabs .= $elements[$ename]['#children'];
			}
		}
		$facetmu .= '</div>';
		$prefix = '<section class="view-section"><ul class="nav nav-tabs">';
		$class = ' active';
		foreach($facettabs as $flabel) {
			$srflabel = strtolower($flabel);
			$prefix .= "<li class=\"facet-{$srflabel}{$class}\"><a href=\".facet-{$srflabel}\" data-toggle=\"tab\"><span class=\"icon shanticon-tree\"></span>{$flabel}</a></li>";
			$class = '';
		}
    $prefix .= '</ul>';      
		$facetmu = $prefix . $facetmu . '</section>';  
		$vars['prefacet'] = $prefacetmu;
		$vars['facetcnt'] = $facetmu;
		$vars['postfacet'] = $postfacetmu;
		//dpm($facetmu, 'facet markup');
	}
}

function sarvaka_mediabase_get_facet_info($fbid) {
	$dmap = facetapi_get_delta_map();
	$searcher = 'apachesolr@solr';
	//$fbid = 'JpJnN1e8hK027W200nfaKp74Qf8XINrv';
	$facet_name = $dmap[$fbid];
	$fparts = explode(':', $facet_name);
	$facet_name = array_pop($fparts);
	$facet = facetapi_facet_load($facet_name, $searcher);
	return array($facet['label'], $facet_name);
}

/**
 * Preprocess function for a NODE
 */
function sarvaka_mediabase_preprocess_node(&$vars) {
	//dpm($vars, 'vars for node');
	// Preprocess Collection Nodes
	if($vars['type'] == 'collection') {
		$style_name = $vars['elements']['field_images'][0]['#image_style'];
		$uri = $vars['elements']['field_images']['#items'][0]['uri'];
		//$path = image_style_path($style_name, $uri);
		$src = image_style_url($style_name, $uri);
		$vars['collimage'] = '<img class="img-thumbnail img-responsive pull-left" src="' . $src . '" />';
		$subcolls = array();
		if(!empty($vars['field_subcoll_root_kmap_id'])) {
			module_load_include('inc','kmap_taxonomy','includes/kmap');
			foreach($vars['field_subcoll_root_kmap_id'] as $n => $t) {
				$kmap = new Kmap($t['taxonomy_term']->kmap_id[LANGUAGE_NONE][0]['value']);
				$kmap->field_name = 'field_subcollection';
				$subcolls[] = _kmap_subject_popover($kmap);
			}
		}
		$vars['subcolls'] = implode(', ', $subcolls);
	}
	// Preprocess a/v nodes:
	else if(in_array($vars['type'], array('audio', 'video'))) {
		// Add collection field to group details
		if(!empty($vars['coll'])) {
			$title = $vars['coll']->title;
			$vars['content']['group_details']['collection'] = array(
				'#type' => 'markup',
				'#markup' => "<div class=\"field field-name-av-collection\">
												<span class=\"icon shanticon-create\" title=\"Collection\"></span>&nbsp;<span class=\"field-label-span\">" .
												t('Collection') . "</span>&nbsp;<a href=\"{$vars['coll']->url}\">{$title}</a></div>",
			);
		}
		// Add Icons 
		if(!empty($vars['content']['group_details']['field_subcollection'])) {
			$vars['content']['group_details']['field_subcollection']['#icon'] = 'create'; 					// subcollection
		}
		if(!empty($vars['content']['group_details']['field_characteristic'])) {
			$vars['content']['group_details']['field_characteristic']['#icon'] = 'subjects'; 				// subjects
		}
		if(!empty($vars['content']['group_details']['field_pbcore_coverage_spatial'])) {
			$vars['content']['group_details']['field_pbcore_coverage_spatial']['#icon'] = 'places';	// places
		}
		// Remove Display of Tags in a/v nodes
		unset($vars['content']['group_details']['field_tags']);
	}
}

/**
 * Preprocess function for a Collection ENTRY FORM
 */
function sarvaka_mediabase_preprocess_collection_node_form(&$vars) {
	drupal_add_css(drupal_get_path('theme', 'sarvaka_mediabase') . '/css/mediabase-edit-form.css');
}

/**
 * Preprocess function for a VIDEO ENTRY FORM
 */
function sarvaka_mediabase_preprocess_video_node_form(&$vars) {
	drupal_add_css(drupal_get_path('theme', 'sarvaka_mediabase') . '/css/mediabase-edit-form.css');
}

/**
 * Preprocess function for a AUDIO ENTRY FORM
 */
function sarvaka_mediabase_preprocess_audio_node_form(&$vars) {
	drupal_add_css(drupal_get_path('theme', 'sarvaka_mediabase') . '/css/mediabase-edit-form.css');
}

/**
 * Views Preprocess
 */
function sarvaka_mediabase_preprocess_views_view(&$vars) {
	$view = $vars['view'];
  if (isset($view->name) && $view->name == 'collections') {
  	//dpm($view, 'view');
		$displ = $view->current_display;
    // Grab the pieces you want and then remove them from the array    
    $header   = $vars['header'];    $vars['header']   = '';
    $filters  = $vars['exposed'];   $vars['exposed']  = '';
    $pager    = $vars['pager'];     $vars['pager']    = '';
    
    // Should be a render array
    
    // Create the view layout switcher
		$faton = ($displ == 'page_list') ? ' on':'';
		$thumbon = ($displ == 'page_thumbs') ? ' on':'';
		$fatpath = ($faton == '') ? $view->display['page_list']->display_options['path'] : '#';
		$thumbpath = ($thumbon == '') ? $view->display['page_thumbs']->display_options['path'] : '#';
		
    $btn1 = "<span class='icon shanticon-list'></span>";
    $btn2 = "<span class='icon shanticon-list4'></span>";
    $btn3 = "<span class='icon shanticon-grid'></span>";
    $switch = "<ul id='view-all-colls-switcher'><li class='fat-list$faton'><a href='$fatpath'>$btn1</a></li><li class='grid$thumbon'><a href='$thumbpath'>$btn3</a></li></ul>";
		// Took out: <!--<li class='thin-list'>$btn2</li>-->
    
    // Put everything in a new element
    $control_box = "<div class='view-all-colls-control-box'><div class='view-all-colls-control-box-row'>";
    $control_box .= "<span class='a view-all-colls-control-box-cell'>$header</span>";
    $control_box .= "<span class='b view-all-colls-control-box-cell'>$filters</span>";
    $control_box .= "<span class='c view-all-colls-control-box-cell'>$switch</span>";
    $control_box .= "<span class='d view-all-colls-control-box-cell'>$pager</span></div></div>\n";
    
    // Attach the new element to the array
    $vars['attachment_before'] = $control_box;
    $vars['attachment_after'] = $pager;
   /* 
    // Add JS and CSS files that will take over behavior
    drupal_add_js(SHANTI_ESSAYS_PATH . '/js/jquery.transit.min.js', 'file');
    drupal_add_js(SHANTI_SARVAKA_TEXTS_PATH . '/js/jquery.cookie.js', 'file');
    drupal_add_js(SHANTI_SARVAKA_TEXTS_PATH . '/js/shanti_essays_page_all_texts.js', $type = 'file', $media = 'all', $preprocess = FALSE);
    drupal_add_css(SHANTI_SARVAKA_TEXTS_PATH . '/css/shanti_essays_page_all_texts.css', $type = 'file', $media = 'all', $preprocess = FALSE);
  */
  }
}

/**
 * Preprocess for spaces preset form
 */
 /*
function sarvaka_mediabase_preprocess_spaces_preset_form(&$vars) {
} */

function sarvaka_mediabase_select($vars) {
	$element = &$vars['element'];
	
	// Deal with Attributes
	$element['#attributes']['class'][] = 'form-control';
  $element['#attributes']['class'][] = 'form-select';
  $element['#attributes']['class'][] = 'ss-select';
  $element['#attributes']['class'][] = 'selectpicker';
  element_set_attributes($element, array('id', 'name', 'size'));
	
	// Process Options into HTML 
	$html = form_select_options($element);
	//   If exposed filter in form, add title as a first option
	if($element['#name'] == 'sort_bef_combine') {
		$element['#options'] = array('label' => $element['#title']) + $element['#options'];
		$html = form_select_options($element);
		$html = str_replace('value="label"', 'data-hidden="true"', $html);
		$html = str_replace('selected="selected"', 'disabled="disabled"', $html);
	}
	
  return '<select' . drupal_attributes($element['#attributes']) . '>' . $html . '</select>';
}

function sarvaka_mediabase_fieldset($vars) {
	$el = $vars['element'];
	if(isset($el['#id']) && $el['#id'] == 'field_collection_item_field_workflow_full_group_workflow') {
		$children = element_children($el);
		$output = '<div class="subgroup"><h5>Media Workflow</h5>';
		foreach($children as $n => $child) {
			$output .= render($el[$child]);
			if($n == 9) {
				$output.= '</div><div class="subgroup"><h5>Cataloging Workflow</h5>';
			}
			if($n == 16) {
				$output.= '</div><div class="subgroup"><h5>Transcript Workflow</h5>';
			}
		}
		$output .= '</div>';
		$vars['element']['#children'] = $output;
	}
	return shanti_sarvaka_fieldset($vars);
}

/*
function sarvaka_mediabase_field__datetime($vars) {
	return render($vars['element']);
}
*/
function sarvaka_mediabase_transcripts_ui_transcript_controls($vars) {
	$out  = "<div class='btn-group' role='group'>";
		$out .= drupal_render($vars['element']['content']['transcript_navigation']);
        $out .= drupal_render($vars['element']['content']['transcript_options']);
	$out .= "</div>";
	// $out .= drupal_render($vars['element']['content']['transcript_search']);
        return $out;
}
function sarvaka_mediabase_transcripts_ui_transcript_options($vars) {
	$out  = "<div class='btn-group' role='group'>";

	//speaker name selector
        $out .= "<button id='speaker-dropdown' type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>";
        $out .= "<span class='glyphicon glyphicon-user'></span> <span class='caret'></span>";
        $out .= "</button>";
	$out .= "<ul class='dropdown-menu' role='menu' aria-labelledby='speaker-dropdown'>";
	$out .= "<li><input type='radio' name='speaker-name-selector' id='bod'> Tibetan</li>";
	$out .= "<li><input type='radio' name='speaker-name-selector' id='wylie'> Wylie</li>"; 	
	$out .= "<li><input type='radio' name='speaker-name-selector' id='none'> None</li>"; 
	$out .= "</ul>";

	//transcript tier selector
//	$out .= "<button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>";
//  $out .= "<span class='glyphicon glyphicon-subtitles'></span> <span class='caret'></span>";
//  $out .= "</button>";
	$out .= "<select multiple class='selectpicker tier-selector' data-header='Languages'>";
	foreach ($vars['element']['data_tiers'] as $key => $val) {
		$out .= "<option value='{$key}'>{$val}</option>";
	}
	$out .= "</select>";

	$out .= "</div>";
	return $out;
}
function sarvaka_mediabase_transcripts_ui_transcript_navigation($vars) {
	$out  = "<div class='btn-group' role='group'>";
	$out .= "<button type='button' class='btn btn-default previous' title='Previous line'><span class='icon shanticon-arrow-left'></span></button>";
	$out .= "<button type='button' class='btn btn-default sameagain' title='Same line'><span class='icon shanticon-spin3'></span></button>";
	$out .= "<button type='button' class='btn btn-default next' title='Next line'><span class='icon shanticon-arrow-right'></span></button>";
	$out .= "</div>";
	return $out;
}
function sarvaka_mediabase_transcripts_ui_transcript_search($vars) {
        $out = drupal_render($vars['element']['search_form']);
        return $out;
}
function sarvaka_mediabase_transcripts_ui_goto_tcu($vars) {
        $mins = floor ($vars['element']['#time'] / 60);
        $secs = $vars['element']['#time'] % 60;
        $time = sprintf ("%d:%02d", $mins, $secs);
        $out = "<a href='" . $vars['element']['#linkurl'] . "' class='btn btn-default' role='button'>";
        $out .= "<span class='glyphicon glyphicon-play'></span> ";
        $out .= "<br>" . $time;
        $out .= "</a>";
        return $out;
}
function sarvaka_mediabase_form_transcripts_ui_viewer_selector_alter(&$form, &$form_state) {
        $form['viewer_selector']['#title'] = '';
        $form['viewer_selector']['#attributes']['data-header'] = t('Select View');
        $form['#attached']['css'][] = drupal_get_path('theme', 'sarvaka_mediabase') .'/css/transcripts-ui-viewer-selector.css';
        $form['#attached']['js'][] = drupal_get_path('theme', 'sarvaka_mediabase') .'/js/transcripts-ui-viewer-selector.js';
}

/**
 * Add js for play transcript button toggle
 */

/* not using for now
function sarvaka_mediabase_preprocess_transcripts_ui_transcript_controls($vars) {
        drupal_add_js("
                (function ($) {
                        $(document).ready(function() {
                                $('.play-transcript').click(function() {
                                        $('i.icon', this).toggleClass('shanticon-play-video shanticon-play-transcript');
                                        if ($(this).hasClass('hidden-transcript')) {
                                                $('span', this).html(Drupal.t('Show<br/>transcript'));
                                        }
                                        else {
                                                $('span', this).html(Drupal.t('Hide<br/>transcript'));
                                        }
                                });
                        })
                }(jQuery));
        ", 'inline');
}
function sarvaka_mediabase_transcripts_ui_play_transcript($vars) {
        $out = "<button class='btn btn-primary btn-icon play-transcript'>";
        $out .= "<i class='icon shanticon-play-video'></i>";
        $out .= "<span>" . t('Hide<br/>transcript') . "</span>";
        $out .= "</button>";
        return $out;
}
*/
