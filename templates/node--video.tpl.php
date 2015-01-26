<?php

/**
 * @file
 * Default theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct URL of the current node.
 * - $display_submitted: Whether submission information should be displayed.
 * - $submitted: Submission information created from $name and $date during
 *   template_preprocess_node().
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type; for example, "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *     listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type; for example, story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode; for example, "full", "teaser".
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a corresponding
 * variable is defined; for example, $node->body becomes $body. When needing to
 * access a field's raw values, developers/themers are strongly encouraged to
 * use these variables. Otherwise they will have to explicitly specify the
 * desired field language; for example, $node->body['en'], thus overriding any
 * language negotiation rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 *
 * @ingroup themeable
 */
?>

<?php
/********* TEASER Display ****************/
if($teaser):
		//dpm($variables, 'in teaser');
		?>
		<li class="shanti-thumbnail video">
	    <div class="shanti-thumbnail-image shanti-field-video">
	      <a href="<?php print $variables['node_url']; ?>" class="shanti-thumbnail-link">
	         <span class="overlay">
	            <span class="icon"></span>
	         </span>
	         <img title="<?php print $title; ?>"
	             alt="<?php print $title; ?>"
	             src="<?php if(isset($variables['thumbnail_url'])) { print $variables['thumbnail_url']; } ?>"
	             typeof="foaf:Image" class="k-no-rotate">
	         <span class="icon shanticon-video"></span>
	      </a>
	    </div>
	    <div class="shanti-thumbnail-info">
	     <div class="body-wrap">
	      <div class="shanti-thumbnail-field shanti-field-title">
	         <span class="field-content"><a href="<?php print $variables['node_url']; ?>"
	             class="shanti-thumbnail-link"><?php print $title; ?></a></span>
	      </div>

	      <div class="shanti-thumbnail-field shanti-field-created">
	          <span class="shanti-field-content"><?php if(!empty($variables['media_create_date'])) {
	          	print date('d M Y', $variables['media_create_date']);
	          } ?></span>
	      </div>

      <?php if(isset($variables['duration'])): ?>
        <div class="shanti-thumbnail-field shanti-field-duration">
         <span class="field-content"> <?php print $variables['duration']['formatted'] ?></span>
        </div>
      <?php endif; ?>

      <?php if($coll): ?>
        <div class="shanti-field shanti-field-group-audience">
            <div class="shanti-field-content"><a href="<?php print $coll->url; ?>"
              class="shanti-thumbnail-link"><a href="<?php print $coll->url; ?>"><?php print $coll->title; ?></a>
            </div>
        </div>
      <?php endif; ?>
    </div> <!-- end body-wrap -->

    <div class="footer-wrap">
      <?php if(isset($variables['place_link'])): ?>
        <div class="shanti-thumbnail-field shanti-field-place">
         <span class="field-content"><span class="icon shanticon-places"></span>
           <?php print render($variables['place_link']); ?>
         </span>
        </div>
      <?php endif; ?>
    </div> <!-- end footer -->
   </div> <!-- end shanti-thumbnail-info -->
</li> <!-- end shanti-thumbnail -->

<?php
else:     /************ FULL Display ***********/
?>
<div id="node-<?php print $node->nid; ?>" class="<?php print $classes; ?>"<?php print $attributes; ?>>

  <?php print $user_picture; ?>

  <?php print render($title_prefix); ?>
  <?php if (!$page): ?>
    <h2<?php print $title_attributes; ?>><a href="<?php print $node_url; ?>"><?php print $title; ?></a></h2>
  <?php endif; ?>
  <?php print render($title_suffix); ?>

  <?php if ($display_submitted): ?>
    <div class="submitted">
      <?php print $submitted; ?>
    </div>
  <?php endif; ?>

  <div class="content"<?php print $content_attributes; ?>>

  <div class="av-main-wrapper row">

  <div class="av-main-video-section col-xs-6 col-md-7">
    <?php
      //dpm($content);
      // We hide the comments and links now so that we can render them later.
      hide($content['comments']);
      hide($content['links']);
			hide($content['field_tags']);
      // Description is compiled in mediabase_preprocess_node and contained in $description variable
      // Not working hide($content['field_pbcore_description']);
    ?>
      <div class="video-row">
          <?php print render($content['field_video']); ?>
      </div>

      <div class="avdesc">
      	<!-- Info/Description row -->
	      <!-- info column -->
	      <div class="avinfo col-xs-12 col-sm-5 col-md-3">		      
	        <div class="avdate"><span class="icon shanticon-calendar"></span>  <?php print date('d M Y', $variables['media_create_date']);  ?></div>
	        <div class="avduration"><span class="icon shanticon-hourglass"></span>  <?php print $node->duration['formatted'];  ?></div>
	        <div class="avrating">
	            <h5>Rating</h5>
	            <?php print render($content['field_rating']); ?>
	        </div>
	        <?php if(!empty($content['service_links'])): ?>
	          <div class="avshare">
	          	<div class="share-links">
	          		<h5>Share <span>&lt;/&gt; embed</span></h5>
	          		<ul>
	          			<?php  print render($content['service_links']);  ?>
	          		</ul>
	          		<!--<p class="hidden"><img src="<?php print $node->thumbnail_url; ?>"/></p>-->
	          	</div>
	          </div>
	        <?php endif; ?>
	      </div> <!-- End of avinfo -->
	      <div class="video-overview col-xs-12 col-sm-7 col-md-9">
	        <h5 class="video-overview-title"><?php print t('Video Overview'); ?></h5>
	        <div class="avpbcoredesc description trim">
	        		<?php print str_replace('clearfix', '', render($content['field_pbcore_description'])); ?>
	        </div>
	        <?php if(isset($coll)): ?>
		        <div class="avcollection">
		        	<span class="icon shanticon-create" title="Collection"></span>
		        	<a href="<?php print $coll->url; ?>"><?php print $coll->title; ?></a>
		        </div>
		      <?php endif; ?>
		      <?php
		      if(!empty($content['group_details']['field_pbcore_coverage_spatial'])): ?>
		        <div class="avplace">
		          	<span class="icon shanticon-places" title="Related Places"></span>
		          	<?php
									$content['group_details']['field_pbcore_coverage_spatial']['#label_display'] = 'hidden';
		          						print render($content['group_details']['field_pbcore_coverage_spatial']);
									$content['group_details']['field_pbcore_coverage_spatial']['#label_display'] = 'above';
									show($content['group_details']['field_pbcore_coverage_spatial']);
		          	?>
		        </div>
		      <?php endif; ?>
	      </div>
	 </div><!--- End of avdesc -->
      </div> <!-- End of av-main-video-section -->
			
			<!-- BEGIN av-main-transcript-section -->
			<div class="av-main-transcript-section col-xs-6 col-md-5">
				<?php if($variables['has_transcript']): ?>
					<?php print render($transcript_controls); ?>
			                <div class='transcript-container'>
			                        <div class='transcript-content' id='transcript'>
			                        	<?php print render($transcript); ?>
			                        </div>
			                </div>
                                <?php endif; ?>
			</div>

     </div> <!-- End of av-main-wrapper -->
  </div> <!-- End of content -->

      <div>
        <ul class="nav nav-tabs nav-justified" role="tablist">
          <li class="active"><a href="#details" role="tab" data-toggle="tab" id="detail-tab"><?php print t('Details'); ?></a></li>
          <li><a href="#related" role="tab" data-toggle="tab" id="related-tab"><?php print t('Related Audio/Videos'); ?></a></li>
        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
          <div class="tab-pane mlt" id="related" data-nid="<?php print $node->nid;?>">
          </div> <!-- End of #related -->
          <div class="tab-pane active" id="details">
          	<div class="btn-group avnode-details-buttons">
          		<button value="click here" class="btn btn-default btn-sm btn-edit-details">Customize</button>
          		<button class="btn btn-default btn-sm btn-toggle-accordion expand">Expand All</button>
          	</div>
          	<div class="panel-group" id="av-details">
		          <?php
		          	$content['group_details']['#attributes']['class'][] = "in";
								// Hiding title in details as it is in banner
								hide($content['group_details']['field_pbcore_title']);
								//dpm($content, 'content');
		          	print render($content);
		          ?>
		        </div>
          </div> <!-- End of #details -->
        </div>
      </div>

  <?php print render($content['links']); ?>

  <?php print render($content['comments']); ?>

</div>
<?php endif; ?>
