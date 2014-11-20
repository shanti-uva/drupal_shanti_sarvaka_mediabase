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

/**
 * Preprocess function for a NODE
 */
function sarvaka_mediabase_preprocess_node(&$vars) {
	// dpm($vars, 'vars');
	// Preprocess Collection Nodes
	if($vars['type'] == 'collection') {
		$style_name = $vars['elements']['field_images'][0]['#image_style'];
		$uri = $vars['elements']['field_images']['#items'][0]['uri'];
		//$path = image_style_path($style_name, $uri);
		$src = image_style_url($style_name, $uri);
		$vars['collimage'] = '<img class="img-responsive pull-left" src="' . $src . '" />';
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
												t('Collection') . "</span>&nbsp;{$title} </div>",
			);
		}
		// Add Icons 
		$vars['content']['group_details']['field_subcollection']['#icon'] = 'create'; 					// subcollection
		$vars['content']['group_details']['field_characteristic']['#icon'] = 'subjects'; 				// subjects
		$vars['content']['group_details']['field_pbcore_coverage_spatial']['#icon'] = 'places';	// places
		// Remove Display of Tags in a/v nodes
		unset($vars['content']['group_details']['field_tags']);
	}
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

/**
 * Change styles applied to transcript search results.
 */
function sarvaka_mediabase_preprocess_apachesolr_search_snippets(&$vars) {
        if ($vars['doc']->entity_type == 'tcu') {
                $button = '<div class="btn-group btn-group-lg btn-group-justified btn-group-transcript">';
                $button .= '<div class="btn-group">';
                $button .= '<button class="btn btn-default btn-icon">';
                $button .= '<i class="icon shanticon-play-video"></i>';
                $button .= '</button>';
                $button .= '</div>';
                $button .= '</div>';
                $vars['transcripts_search_snippet']['#linktext'] = $button;
                $vars['transcripts_search_snippet']['#attached'] = array(
                        'css' => array(drupal_get_path('theme', 'sarvaka_mediabase') .'/css/transcripts-search-snippet.css'),
                );
        }
}

/**
 * Add js for play transcript button toggle
 */
function sarvaka_mediabase_preprocess_transcripts_video_controls($vars) {
        drupal_add_js("
                (function ($) {
                        $('.play-transcript').click(function() {
                                $('i.icon', this).toggleClass('shanticon-play-video shanticon-play-transcript');
                                if ($(this).hasClass('without-transcript')) {
                                        $('span', this).html(Drupal.t('Show<br/>transcript'));
                                }
                                else {
                                        $('span', this).html(Drupal.t('Hide<br/>transcript'));
                                }
                        });
                }(jQuery));
        ", 'inline');
}

function sarvaka_mediabase_transcripts_video_controls($vars) {
        $out = "<div style='width: 480px;' class='btn-group btn-group-lg btn-group-justified btn-group-transcript'>";
        $out .= "<div class='btn-group'>" .$vars['element']['#play']. "</div>";
        $out .= "<div class='btn-group'>" .$vars['element']['#prev']. "</div>";
        $out .= "<div class='btn-group'>" .$vars['element']['#same']. "</div>";
        $out .= "<div class='btn-group'>" .$vars['element']['#next']. "</div>";
        $out .= "</div>";
        return $out;
}
function sarvaka_mediabase_transcripts_play_transcript($vars) {
        $out = "<button class='btn btn-primary btn-icon play-transcript without-transcript'>";
        $out .= "<i class='icon shanticon-play-video'></i>";
        $out .= "<span>" . t('Hide<br/>transcript') . "</span>";
        $out .= "</button>";
        return $out;
}
function sarvaka_mediabase_transcripts_previous_tcu($vars) {
        $out = "<button class='btn btn-default btn-icon previous' title='Previous line'>";
        $out .= "<i class='icon shanticon-arrow-left'></i>";
        $out .= "</button>";
        return $out;
}
function sarvaka_mediabase_transcripts_same_tcu($vars) {
        $out = "<button class='btn btn-default btn-icon sameagain' title='Same line'>";
        $out .= "<i class='icon shanticon-spin3'></i>";
        $out .= "</button>";
        return $out;
}
function sarvaka_mediabase_transcripts_next_tcu($vars) {
        $out = "<button class='btn btn-default btn-icon next' title='Next line'>";
        $out .= "<i class='icon shanticon-arrow-right'></i>";
        $out .= "</button>";
        return $out;
}
function sarvaka_mediabase_form_transcripts_controller_mode_selector_alter(&$form, &$form_state) {
        $form['mode_selector']['#title'] = '';
        $form['mode_selector']['#attributes']['data-header'] = t('Select a transcript view');
        $form['#attached']['css'][] = drupal_get_path('theme', 'sarvaka_mediabase') .'/css/transcripts-mode-selector.css';
        $form['#attached']['js'][] = drupal_get_path('theme', 'sarvaka_mediabase') .'/js/transcripts-mode-selector.js';
}
function sarvaka_mediabase_form_transcripts_controller_tier_selector_alter(&$form, &$form_state) {
        $form['tier_selector']['#title'] = '';
        $form['tier_selector']['#attributes']['multiple'] = '';
        $form['tier_selector']['#attributes']['data-header'] = t('Choose languages to display');
        $form['tier_selector']['#attributes']['data-selected-text-format'] = 'count > 2';
        $form['#attached']['css'][] = drupal_get_path('theme', 'sarvaka_mediabase') .'/css/transcripts-tier-selector.css';
}
