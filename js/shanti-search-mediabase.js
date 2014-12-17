


  /**
   * Fancy Tree Init OLD from theme for example
   *
   * Initialize Fancy tree in fly out
   *
   *     API call examples:
   *       Get a node: var node = $.ui.fancytree.getNode(el);
   *      Activate a node: node.setActive(true); // Performs activate action too
   *      Show a node: node.makeVisible(); // Opens tree to node and scrolls to it without performing action
   **/
  /** NDG: Taking out Nov. 10 because no longer need fancy tree in theme:
  Drupal.behaviors.shanti_sarvaka_fancytree = {
    attach: function (context, settings) {

      // Facet Tree in Search Flyout
      var divs = $(Drupal.settings.shanti_sarvaka.ftListSelector).parent();

      divs.each(function() {
        // Find the container div for the fancy tree
        var facettype = $(this).children('ul').attr('id').split('-').pop();
        $(this).attr('id', facettype + '-facet-content-div');

        // Initiate the Fancy Tree
        var tree = $(this).fancytree({
          activeVisible: true, // Make sure, active nodes are visible (expanded).
          aria: false, // Enable WAI-ARIA support.
          autoActivate: true, // Automatically activate a node when it is focused (using keys).
          autoCollapse: false, // Automatically collapse all siblings, when a node is expanded.
          autoScroll: true, // Automatically scroll nodes into visible area.
          activate: function(event, data) {
            var node = data.node;
            //console.info(node.data);
            $('i.icon.shanticon-cancel').remove(); // remove existing cancel icons
            loadFacetSearch(node.data);
            Drupal.ShantiSarvaka.searchTabHeight();
            return false;
          },
          clickFolderMode: 3, // 1:activate, 2:expand, 3:activate and expand, 4:activate (dblclick expands)
          checkbox: false, // Show checkboxes.
          debugLevel: 1, // 0:quiet, 1:normal, 2:debug
          disabled: false, // Disable control
          extensions: ["glyph", "filter"],
          filter: { mode: 'hide' },
          generateIds: true, // Generate id attributes like <span id='fancytree-id-KEY'>
          glyph: {
              map: {
                  doc: "",
                  docOpen: "",
                  error: "glyphicon glyphicon-warning-sign",
                  expanderClosed: "glyphicon glyphicon-plus-sign",
                  expanderOpen: "glyphicon glyphicon-minus-sign",
                  folder: "",
                  folderOpen: "",
              }
          },
          idPrefix: "ftid", // Used to generate node id´s like <span id='fancytree-id-<key>'>.
          icons: true, // Display node icons.
          keyboard: true, // Support keyboard navigation.
          keyPathSeparator: "/", // Used by node.getKeyPath() and tree.loadKeyPath().
          minExpandLevel: 1, // 1: root node is not collapsible
          selectMode: 2, // 1:single, 2:multi, 3:multi-hier
          tabbable: true, // Whole tree behaves as one single control
          titlesTabbable: false, // Node titles can receive keyboard focus
        });
        Drupal.settings.shanti_sarvaka.fancytrees.push($(tree).fancytree('getTree'));
      });

      // Set facet link title attributes on mouseover
      $('ul.fancytree-container').on('mouseover', 'span.fancytree-title', function() {
        if($(this).find('span.element-invisible').length == 1) {
          var title = $(this).find('span.element-invisible').text();
          $(this).attr('title', title);
          $(this).find('span.element-invisible').remove();
        }
      });

      // Initiate Facet Label Search Toggles
      $('div.block-facetapi').on('click', 'button.toggle-facet-label-search', function(e) {
        if($(this).prev('input').is(':hidden')) {
          $(this).prev('input').slideDown({duration: 400, queue: false}).animate({ width: '50%', queue: false });
          e.preventDefault();
        } else {
          $(this).prev('input').animate({ width: '0%', queue: false }).slideUp({duration: 400, queue: false});
          e.preventDefault();
        }
      });

      // When text is entered into the facet label filter box perform a filter
      $('div.block-facetapi').on('keyup', 'input.facet-label-search', function (e) {
        var sval = $(this).val();
        var tree = $(this).parents('div.block-facetapi').find('div.content').fancytree('getTree');
        // If sval is a number search for facet by id
        if(!isNaN(sval)) {
          tree.applyFilter(function (node) {
            if(node.data.fid == sval) {
              return true;
            }
            return false;
          });
        // Search for string if over 2 chars long
        } else if(sval.length > 2) {
          $('span.fancytree-title mark').each(
              function () {
                var parent = $(this).parent();
                var children = parent.children('.facet-count, .element-invisible').remove();
                parent.text(parent.text());
                parent.append(children);
              }
          );
          tree.applyFilter(sval);
          $('span.fancytree-title').highlight(sval, { element: 'mark' });
        // if sval is empty clear filter
        } else if(sval.length == 0) {
          tree.clearFacetFilter();
        }
      });

      // Activate the remove facet links
      $('div.block-facetapi').on('click', 'i.icon.shanticon-cancel', function() {
        //console.log('clicked');
        var tree = $(this).parents('ul.ui-fancytree').parents('div.content').fancytree('getTree');
        var nodeId = $(this).parents('li').eq(0).attr('id').replace('ftid','');
        var node = tree.getNodeByKey(nodeId);
        tree.clearFacetFilter();
        //node.setActive(true);
        node.setExpanded(true);
        node.setSelected(false);
        //$(node.span).removeClass('fancytree-active fancytree-focused fancytree-selected');
        $('article.main-content section.content-section').html($('#original-content').html());
        $('#original-content').remove();
        $(this).remove();
        //console.log(tree, nodeId, node.data);
      });
    }
  };
*/