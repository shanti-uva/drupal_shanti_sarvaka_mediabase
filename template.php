<?php

/**
 * @file
 * template.php
 */
define('MBFRAME', 'mbframe'); // Name of MBFRAME Cookie

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

/**
 * Implements theme_breadcrumb
 * 		Change First Breadcrumb to link to collection/group page for group admin pages
 */
function sarvaka_mediabase_breadcrumb($variables) {
	$cp = current_path();
	// Change First Breadcrumb to link to collection/group page for group admin pages
	if (drupal_match_path($cp, 'group/node/*/admin/**')) {
		$bcs = &$variables['breadcrumb'];
		if (count($bcs) > 1 && $menuitem = menu_get_item($cp)) {
			$node = node_load($menuitem['map'][2]); // 3rd item in menu item map is the group node id
			$url = 'node/' . $node->nid; 
			$bcs[1] = l($node->title, $url);
		}
	}
	return shanti_sarvaka_breadcrumb($variables);
}

function sarvaka_mediabase_form_alter(&$form, &$form_state, $form_id) {
	// Add bo class to text areas for Tibetan descriptions
	if ($form_id == 'video_node_form' || $form_id == 'audio_node_form' ) {
		foreach($form['field_pbcore_description'][$form['field_pbcore_description']['#language']] as $key => &$item) {
			if (is_numeric($key) && !empty($item['field_language'][$item['field_language']['#language']]['#default_value'][0])) {
				if ($item['field_language'][$item['field_language']['#language']]['#default_value'][0] == 'Tibetan') {
					$item['field_description']['#attributes']['class'][] = 'bo'; 
				}
			}
		}
	}

	if($form_id == "views_exposed_form") {
		//$form['#ajax']['wrapper'] = 'block-views-browse-media-home-block';
	}
}

/**
 * Implements hook_preprocess_html
 */
function sarvaka_mediabase_preprocess_html(&$vars) {
	// Add js and css to detect if in iframe and if so hide header elements
	$mpath = drupal_get_path('theme', 'sarvaka_mediabase');
	// drupal_add_css($mpath . '/css/mb-iframe.css', array('group' => CSS_THEME)); - mf8yk - deprecated 01/15/15 moved this CSS to the shanti-main-mb 
	drupal_add_js($mpath . '/js/mb-iframe.js', array('weight' => -99, 'group' => JS_DEFAULT));
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
 * Preprocess User Profile
 */
function sarvaka_mediabase_preprocess_user_profile(&$variables) {
    $variables['user_profile']['group_audience']['#weight'] = 40; // Put Group Audience Last
}

/**
 * Preprocess function for a NODE
 */
function sarvaka_mediabase_preprocess_node(&$vars) {
    
	// Preprocess Collection and Subcollection Nodes
	$ntype = $vars['type'];
    $mode = $vars['view_mode'];
    $node = $vars['node'];
    if(in_array($ntype, array('collection', 'subcollection'))) {
        if($mode == 'teaser') {
        	    	$vars['theme_hook_suggestions'][] = 'node__collection__teaser';   // Have them both use the same teaser template
    	        // Get thumbnail image
            if (empty($vars['field_general_featured_image']) || !isset($vars['field_general_featured_image']['und'][0]['uri'])) {
                $vars['thumbnail_url'] = '/sites/all/modules/mediabase/images/collections-generic.png';
            } else {
                $uri = $vars['field_general_featured_image']['und'][0]['uri'];
                $style = 'av_gallery_thumb'; // Style defined in mb_structure update 7002
                 $vars['thumbnail_url'] = image_style_url($style, $uri);
            }
            // Deal with the body/description (truncate)
            $vars['desc'] = "";
            $body = array_shift(field_get_items('node', $node, 'body'));
            if (!empty($body) && isset($body['safe_value'])) {
                $desc = strip_tags($body['safe_value']);
                $vars['desc'] = (strlen($desc) > 0) ? substr($desc, 0, 60) . "..." : "";
            }
            //dpm($vars, 'vars');
            // Get the number of items in the collection from mb_structure.module
            $vars['item_count'] = get_items_in_collection($vars['nid']);
            
           $vars['coll'] = ($ntype == "subcollection") ? get_collection_ancestor_node($node) : FALSE;
          
        }
    	}
	// Preprocess a/v nodes:
	else if(in_array($ntype, array('audio', 'video'))) {
	    
		// Teasers
		if($mode == 'teaser') {
			// Get Title language and add as variable for template
			$ew = entity_metadata_wrapper('node', $node);
			try {
				$vars['title_lang'] = lang_code($ew->field_pbcore_title[0]->field_language->value());
			} catch (EntityMetadataWrapperException $emwe) {
				watchdog('sarvaka mediabase', 'No field language in entity wrapper for node ' . $node->nid);
			}
			// Truncate title in teasers
			if(strlen($vars['title']) > 75) {
				$vars['title'] = truncate_utf8($vars['title'], 75, TRUE, TRUE);
			}
		}
        
		// Add collection field to group details
		if(!empty($vars['coll'])) {
			$vars['coll_title'] = $vars['coll']->title;
			// Truncate collection title in teaser if item title is longer than 60 chars
			if($vars['view_mode'] == 'teaser') {
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
		if(!empty($vars['content']['group_details']['field_subcollection_new'])) {
			$vars['content']['group_details']['field_subcollection_new']['#icon'] = 'create'; 					// subcollection
		}
		if(!empty($vars['content']['group_details']['field_subject'])) {
			$vars['content']['group_details']['field_subject']['#icon'] = 'subjects'; 				// subjects
		}
		if(!empty($vars['content']['group_details']['field_location'])) {
			$vars['content']['group_details']['field_location']['#icon'] = 'places';	// places
		}
		// Remove Display of Tags in a/v nodes
		unset($vars['content']['group_details']['field_tags']);
		
		// Add Label as prefix for related media so it doesn't repeat for each on, if related media exist
		if (!empty($vars['content']['group_details']['field_pbcore_relation'])) {
			$vars['content']['group_details']['field_pbcore_relation']['#prefix'] = '<div class="field"><span class="field-label-span">Related Media</span></div>';
		}			 	
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
    // Browse media view: includes my media and my collections
	if (isset($view->name) && $view->name == 'browse_media') {
		$query = $view->query;
		// Make sure text search box is only size 15 on home page filter
	    $filters  = $vars['exposed'];   $vars['exposed']  = '';
	  	$filters = str_replace('name="title" value="" size="30"', 'name="title" value="" size="15"', $filters);
		
		// Set Dropdown selected value
		$field = $query->orderby[0]['field'];
		$direction = $query->orderby[0]['direction'];
		$selval = $query->fields[$field]['field'] . ' ' . $direction;
		$filters = str_replace("value=\"{$selval}\"", "value=\"{$selval}\" selected=\"selected\"", $filters);
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

/**
 * Converts a language's English name into its two-letter language code
 */
function lang_code($lname) {
	// Search through list of enabled languages
	foreach (language_list() as $cd => $lang) {
		if ($lang->name == $lname) { return $cd; }
	}
	// Account for when I18n languages have not been enabled but still used in field collection
	if ($lname == "Tibetan") {return "bo";}
	if ($lname == "Chinese") {return "zh";}
	return "en";
}
