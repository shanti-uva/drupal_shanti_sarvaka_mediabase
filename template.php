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
