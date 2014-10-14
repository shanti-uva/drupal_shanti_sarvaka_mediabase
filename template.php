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

/**
 * Preprocess function for a NODE
 */
function sarvaka_mediabase_preprocess_node(&$vars) {
	dpm($vars, 'vars');
	// Add collection field to group details
	if(!empty($vars['coll'])) {
		$title = $vars['coll']->title;
		$vars['content']['group_details']['collection'] = array(
			'#type' => 'markup',
			'#markup' => "<div class=\"field field-name-av-collection\">
											<span class=\"field-label-span\"><span class=\"icon shanticon-create\" title=\"Collection\"></span> " .
											t('Collection:') . "</span> {$title} </div>",
		);
	}
	// Add Icons 
	$vars['content']['group_details']['field_subcollection']['#icon'] = 'create'; 					// subcollection
	$vars['content']['group_details']['field_characteristic']['#icon'] = 'subjects'; 				// subjects
	$vars['content']['group_details']['field_pbcore_coverage_spatial']['#icon'] = 'places';	// places
	 
	
}

/**
 * Preprocess function for a VIDEO ENTRY FORM
 */
function sarvaka_mediabase_preprocess_video_node_form(&$vars) {
	/*dpm($vars['form']);
	$vars['messages'] = theme_status_messages(array('display' => 'status'));*/
	
}

/**
 * Preprocess function for a AUDIO ENTRY FORM
 */
function sarvaka_mediabase_preprocess_audio_node_form(&$vars) {
	// Add variables for audio node form processing here
}