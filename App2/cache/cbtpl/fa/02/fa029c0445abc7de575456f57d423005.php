

  <?php
$params = dcCore::app()->ctx->post_params;
dcCore::app()->ctx->pagination = dcCore::app()->blog->getPosts($params,true); unset($params);
?>
<?php if (dcCore::app()->ctx->pagination->f(0) > dcCore::app()->ctx->posts->count()) : ?>
    <nav class="pagination" aria-label="<?php echo __('Pagination'); ?>" role="navigation">
      
        <?php if(!context::PaginationEnd()) : ?>
          <a href="<?php echo context::global_filters(context::PaginationURL(1),array (
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
  'offset' => '+1',
),'PaginationURL'); ?>" class="prev"><span aria-hidden="true">&#171; </span><?php echo __('previous entries'); ?></a> -
        <?php endif; ?>
      
      
        <span class="visually-hidden"><?php echo __('Active page'); ?></span><?php echo __('page'); ?> <?php echo context::global_filters(context::PaginationPosition(0),array (
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
),'PaginationCurrent'); ?> <?php echo __('of'); ?> <?php echo context::global_filters(context::PaginationNbPages(),array (
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
),'PaginationCounter'); ?>
      
      
        <?php if(!context::PaginationStart()) : ?> - <a href="<?php echo context::global_filters(context::PaginationURL(-1),array (
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
  'offset' => '-1',
),'PaginationURL'); ?>" class="next">
          <?php echo __('next entries'); ?><span aria-hidden="true"> &#187;</span></a>
        <?php endif; ?>
      
    </nav>
  <?php endif; ?>


