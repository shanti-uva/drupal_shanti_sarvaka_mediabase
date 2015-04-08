<?php

/**
 * @file
 * template.php
 */
define(MBFRAME, 'mbframe'); // Name of MBFRAME Cookie

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

function sarvaka_mediabase_preprocess_html(&$vars) {
	$mpath = drupal_get_path('theme', 'sarvaka_mediabase');
	drupal_add_css($mpath . '/css/mb-iframe.css', array('group' => CSS_THEME));
	drupal_add_js($mpath . '/js/mb-iframe.js', array('group' => JS_THEME));
	if(isset($_GET[MBFRAME]) && $_GET[MBFRAME] == "on") {
		$vars['classes_array'][] ='in-frame';
	} 
}

function sarvaka_mediabase_preprocess_block(&$vars) {
	/*
	if(!empty($vars['block_html_id']) && strpos($vars['block_html_id'], 'browse-media-home') > -1) {
		dpm($vars, 'browse media home block vars');
	}
	*/
	// Facet labels
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
		$fct = 0;
		foreach($children as $ename) {
			if(strpos($ename, 'facetapi') > -1) {
				$fct++;
				$facets_done = TRUE;
				list($flabel, $fname) = sarvaka_mediabase_get_facet_info($elements[$ename]['#block']->delta);
				$srflabel = strtolower($flabel);
				$facettabs[] = $flabel;
				$active = ($fct == 1) ? " active":"";
				$facetmu .= "<div class=\"facet-{$srflabel} tab-pane{$active}\"><div class=\"kmaps-tree facet-{$srflabel} view-wrap\">{$elements[$ename]['#children']}</div></div>";
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
	if($vars['type'] == 'collection' || $vars['type'] == 'team') {
		$vars['collimage'] = '';
		if(isset($vars['elements']['field_images'][0]['#image_style']) && isset($vars['elements']['field_images']['#items'][0]['uri'])) {
			$style_name = $vars['elements']['field_images'][0]['#image_style'];
			$uri = $vars['elements']['field_images']['#items'][0]['uri'];
			$src = image_style_url($style_name, $uri);
			$vars['collimage'] = '<img class="img-thumbnail img-responsive pull-left" src="' . $src . '" />';
		}
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
		// Truncate title in teasers
		if($vars['view_mode'] == 'teaser' && strlen($vars['title']) > 75) {
			$vars['title'] = truncate_utf8($vars['title'], 75, TRUE, TRUE);
		}
		 
		// Team link
		if(!empty($vars['team'])) {
			$path = drupal_get_path_alias('node/' . $vars['team']->nid);
			$team_link = l($vars['team']->title, $path);
			$vars['content']['group_details']['team'] = array(
				'#type' => 'markup',
				'#markup' => "<div class=\"field field-name-av-team\">
												<span class=\"icon shanticon-create\" title=\"Team\"></span>&nbsp;<span class=\"field-label-span\">" .
												t('Team') . "</span>&nbsp;{$team_link}</div>",
			);
		}
		
		// Add collection field to group details
		if(!empty($vars['coll'])) {
			$vars['coll_title'] = $vars['coll']->title;
			// Truncate collection title in teaser if item title is longer than 60 chars
			if($vars['view_mode'] == 'teaser' && strlen($vars['title']) > 50) {
				$vars['coll_title'] = truncate_utf8($vars['coll_title'], 32, TRUE, TRUE);
			}
			$vars['content']['group_details']['collection'] = array(
				'#type' => 'markup',
				'#markup' => "<div class=\"field field-name-av-collection\">
												<span class=\"icon shanticon-create\" title=\"Collection\"></span>&nbsp;<span class=\"field-label-span\">" .
												t('Collection') . "</span>&nbsp;<a href=\"{$vars['coll']->url}\">{$vars['coll_title']}</a></div>",
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

	// Author info
	$uid = $vars['uid'];
	$uname = $uid;
	$author = user_load($uid);
	if(!empty($author->realname)) {
		$uname = $author->realname; 
	} elseif (!empty($author->name)) {
		$uname = $author->name;
	}
	$vars['user_link'] = l($uname, "user/$uid");
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
    $control_box .= "<span class='a view-all-colls-control-box-cell col-xs-12 col-sm-3'>$header</span>";
    $control_box .= "<span class='b view-all-colls-control-box-cell col-xs-12 col-sm-3'>$filters</span>";
    $control_box .= "<span class='c view-all-colls-control-box-cell col-xs-4 col-sm-3'>$switch</span>";
    $control_box .= "<span class='d view-all-colls-control-box-cell col-xs-8 col-sm-3'>$pager</span></div></div>\n";
    
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
  // Home page view Tweaks
  } else if(!empty($vars['name']) && $vars['name'] =='browse_media') {
  	//dpm($vars, 'pp view');
		$query = $view->query;
    // Grab the pieces you want and then remove them from the array    
	    /*$header   = $vars['header'];    $vars['header']   = '';
	    $pager    = $vars['pager'];     $vars['pager']    = '';
			$vars['header']   = $header;
			$vars['pager']    = $pager;*/
			
		// Make sure text search box is only size 15 on home page filter
    $filters  = $vars['exposed'];   $vars['exposed']  = '';
  	$filters = str_replace('name="title" value="" size="30"', 'name="title" value="" size="15"', $filters);
		
		// Set Dropdown selected value
		$field = $query->orderby[0]['field'];
		$direction = $query->orderby[0]['direction'];
		$selval = $query->fields[$field]['field'] . ' ' . $direction;
		$filters = str_replace("value=\"{$selval}\"", "value=\"{$selval}\" selected=\"selected\"", $filters);
		/*$filters = str_replace('Date Created Asc', 'Date Created &#11014;', $filters);
		$filters = str_replace('Date Created Desc', 'Date Created &#11015;', $filters);
		$filters = str_replace('Asc', '(A-Z)', $filters);
		$filters = str_replace('Desc', '(Z-A)', $filters);*/
		//dpm($filters, 'filters');
		$vars['exposed']  = $filters;
		
	// List views of Media By Kmap
  } else if(isset($view->name) && $view->name == 'media_by_kmap') {
  	$type = $vars['display_id'];
  	$title = "";
		$kmid = $view->args[0];
		if($type == 'list_places') {
			module_load_include('inc', 'mb_location', 'mb_location');
			$place = _get_kmap_place($kmid);
			$title .= t("Resources Associated with the Place: ") . $place->header;
			$parents = array_reverse(fetch_place_dict_details($kmid), TRUE);
			$vars['lineage'] = '<div class="lineage"><span class="label">' . t("Places Tree:") . '<span> <span class="links">';
			$ppath = variable_get('kmaps_site_places', 'http://badger.drupal-dev.shanti.virginia.edu/places');
			$ppath .= variable_get('kmaps_site_path_format', '/%d/overview/nojs');
			$pout = "";
			foreach($parents as $pdid => $pname) {
				$pout .= (strlen($pout) > 0) ? ' > ': '';
				$link = sprintf($ppath, $pdid);
				$pout .= "<a href=\"$link\" target=\"_blank\">$pname</a>";
			}
			$vars['lineage'] .= $pout . '</span></div>';
		} else if($type == 'list_subcollections' || $type == 'list_subjects') {
			module_load_include('inc','kmap_taxonomy','includes/kmap');
			$kmap = new Kmap($kmid);
			$t = $kmap->get_term();
			$typestr = ($type == 'list_subjects') ? t("Subject") : t("Subcollection");
			$title .= t("Resources Associated with the @typestr: ", array('@typestr' => $typestr)) .  $t->name;
			$lineage = $kmap->render_kmap_lineage(Kmap::KMAP_LINEAGE_FULL, TRUE);
			$vars['lineage'] = '<div class="lineage"><span class="label">' . t("Subject Tree:") . '<span> <span class="links">' . $lineage . '</span></div>';
		}
		$vars['title'] = $title;
  }
}

function decodeUniChar($unicodeChar) {
	return json_decode('"'.$unicodeChar.'"');
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

/**
 * Impelments hook_preprocess_field:
 * 	Changes labels for field collection to use "role" for label (or in the case of publisher "rome")
 */
function sarvaka_mediabase_preprocess_field(&$vars) {
	$el = &$vars['element'];
	if($el['#field_name'] == 'field_creator') {
		$ew = entity_metadata_wrapper($el['#entity_type'], $el['#object']);
		$label = $ew->field_creator_role->value();
		if (strlen($label) > 0) { $vars['label'] = $label; }
		
	} else if($el['#field_name'] == 'field_contributor') {
		$ew = entity_metadata_wrapper($el['#entity_type'], $el['#object']);
		$label = $ew->field_contributor_role->value();
		if (strlen($label) > 0) { $vars['label'] = t('Contributing ') . $label; }
		
	} else if($el['#field_name'] == 'field_publisher') {
		$ew = entity_metadata_wrapper($el['#entity_type'], $el['#object']);
		$label = $ew->field_publisher_rome->value();
		if (strlen($label) > 0) { $vars['label'] = $label; }
	}
}

/*
function sarvaka_mediabase_field__datetime($vars) {
	return render($vars['element']);
}
*/
