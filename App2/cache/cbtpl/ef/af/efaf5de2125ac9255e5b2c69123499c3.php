

  

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  
  
    <link rel="preload" href="<?php echo context::global_filters(dcCore::app()->blog->settings->system->themes_url."/".dcCore::app()->blog->settings->system->theme,array (
  0 => NULL,
  'encode_xml' => 0,
  'encode_html' => 0,
  'cut_string' => 0,
  'lower_case' => 0,
  'upper_case' => 0,
  'encode_url' => 0,
  'remove_html' => 0,
  'capitalize' => 0,
  'strip_tags' => 0,
),'BlogThemeURL'); ?>/style.css" as="style" />
    <link rel="stylesheet" type="text/css" href="<?php echo context::global_filters(dcCore::app()->blog->settings->system->themes_url."/".dcCore::app()->blog->settings->system->theme,array (
  0 => NULL,
  'encode_xml' => 0,
  'encode_html' => 0,
  'cut_string' => 0,
  'lower_case' => 0,
  'upper_case' => 0,
  'encode_url' => 0,
  'remove_html' => 0,
  'capitalize' => 0,
  'strip_tags' => 0,
),'BlogThemeURL'); ?>/style.css" media="screen" />
  
  
    <link rel="stylesheet" type="text/css" href="<?php echo context::global_filters(dcCore::app()->blog->getQmarkURL(),array (
  0 => NULL,
  'encode_xml' => 0,
  'encode_html' => 0,
  'cut_string' => 0,
  'lower_case' => 0,
  'upper_case' => 0,
  'encode_url' => 0,
  'remove_html' => 0,
  'capitalize' => 0,
  'strip_tags' => 0,
),'BlogQmarkURL'); ?>pf=print.css" media="print" />
  
  
    <?php if(dcCore::app()->blog->settings->system->jquery_needed) : ?>
      <link rel="preload" href="<?php echo context::global_filters(dcCore::app()->blog->getQmarkURL(),array (
  0 => NULL,
  'encode_xml' => 0,
  'encode_html' => 0,
  'cut_string' => 0,
  'lower_case' => 0,
  'upper_case' => 0,
  'encode_url' => 0,
  'remove_html' => 0,
  'capitalize' => 0,
  'strip_tags' => 0,
),'BlogQmarkURL'); ?>pf=<?php echo context::global_filters(dcCore::app()->blog->getJsJQuery(),array (
  0 => NULL,
  'encode_xml' => 0,
  'encode_html' => 0,
  'cut_string' => 0,
  'lower_case' => 0,
  'upper_case' => 0,
  'encode_url' => 0,
  'remove_html' => 0,
  'capitalize' => 0,
  'strip_tags' => 0,
),'BlogJsJQuery'); ?>/jquery.js" as="script" />
      <script src="<?php echo context::global_filters(dcCore::app()->blog->getQmarkURL(),array (
  0 => NULL,
  'encode_xml' => 0,
  'encode_html' => 0,
  'cut_string' => 0,
  'lower_case' => 0,
  'upper_case' => 0,
  'encode_url' => 0,
  'remove_html' => 0,
  'capitalize' => 0,
  'strip_tags' => 0,
),'BlogQmarkURL'); ?>pf=<?php echo context::global_filters(dcCore::app()->blog->getJsJQuery(),array (
  0 => NULL,
  'encode_xml' => 0,
  'encode_html' => 0,
  'cut_string' => 0,
  'lower_case' => 0,
  'upper_case' => 0,
  'encode_url' => 0,
  'remove_html' => 0,
  'capitalize' => 0,
  'strip_tags' => 0,
),'BlogJsJQuery'); ?>/jquery.js"></script>
    <?php endif; ?>
  
  
    <link rel="preload" href="<?php echo context::global_filters(dcCore::app()->blog->getQmarkURL(),array (
  0 => NULL,
  'encode_xml' => 0,
  'encode_html' => 0,
  'cut_string' => 0,
  'lower_case' => 0,
  'upper_case' => 0,
  'encode_url' => 0,
  'remove_html' => 0,
  'capitalize' => 0,
  'strip_tags' => 0,
),'BlogQmarkURL'); ?>pf=util.js" as="script" />
    <script src="<?php echo context::global_filters(dcCore::app()->blog->getQmarkURL(),array (
  0 => NULL,
  'encode_xml' => 0,
  'encode_html' => 0,
  'cut_string' => 0,
  'lower_case' => 0,
  'upper_case' => 0,
  'encode_url' => 0,
  'remove_html' => 0,
  'capitalize' => 0,
  'strip_tags' => 0,
),'BlogQmarkURL'); ?>pf=util.js"></script>
  
  
    <?php try { echo dcCore::app()->tpl->getData('user_head.html'); } catch (Exception $e) {} ?>

  
  
    <?php if (dcCore::app()->hasBehavior('publicHeadContent')) { dcCore::app()->callBehavior('publicHeadContent',dcCore::app(),dcCore::app()->ctx);} ?>
  


