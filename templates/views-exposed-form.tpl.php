<?php
// $Id: views-exposed-form.tpl.php,v 1.4 2008/05/07 23:00:25 merlinofchaos Exp $
/**
 * @file views-exposed-form.tpl.php
 *
 * This template handles the layout of the views exposed filter form.
 *
 * Variables available:
 * - $widgets: An array of exposed form widgets. Each widget contains:
 * - $widget->label: The visible label to print. May be optional.
 * - $widget->operator: The operator for the widget. May be optional.
 * - $widget->widget: The widget itself.
 * - $button: The submit button for the form.
 *
 * @ingroup views_templates
 */
?>
<?php if (!empty($q)): ?>
  <?php
    // This ensures that, if clean URLs are off, the 'q' is added first so that
    // it shows up first in the URL.
    print $q;
  ?>
<?php endif; ?>
<div class="views-exposed-form">
  <div class="views-exposed-widgets clear-block row">
    <?php foreach($widgets as $id => $widget): ?>
      <div class="views-exposed-widget col-lg-3 col-md-4 col-sm-6 col-xs-12">
        <?php if (!empty($widget->label)): ?>
          <label>
            <?php print $widget->label; ?>
          </label>
        <?php endif; ?>
        <?php if (!empty($widget->operator)): ?>
          <div class="views-operator">
            <?php print $widget->operator; ?>
          </div>
        <?php endif; ?>
        <div class="views-widget">
          <?php print $widget->widget; ?>
        </div>
      </div>
    <?php endforeach; ?>
    <div class="views-exposed-widget">
      <?php print $button ?>
    </div>
  </div>
</div>