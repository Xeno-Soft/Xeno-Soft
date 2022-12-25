

  <article id="p<?php echo context::global_filters(dcCore::app()->ctx->posts->post_id,array (
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
),'EntryID'); ?>" class="post <?php echo ((dcCore::app()->ctx->posts->index()+1)%2 ? "odd" : ""); ?> <?php if (dcCore::app()->ctx->posts->index() == 0) { echo 'first'; } ?> <?php if((boolean)dcCore::app()->ctx->posts->isRepublished()) : ?>updated<?php endif; ?> full" lang="<?php if (dcCore::app()->ctx->posts->post_lang) { echo context::global_filters(dcCore::app()->ctx->posts->post_lang,array (
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
),'EntryLang'); } else {echo context::global_filters(dcCore::app()->blog->settings->system->lang,array (
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
),'EntryLang'); } ?>" role="article">
    
      <header>
        

          <?php if (dcCore::app()->ctx->posts->firstPostOfDay()) : ?>
            <p class="post-day-date"><time datetime="<?php echo context::global_filters(dcCore::app()->ctx->posts->getISO8601Date(''),array (
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
  'iso8601' => '1',
),'EntryDate'); ?>"><?php echo context::global_filters(dcCore::app()->ctx->posts->getDate('',''),array (
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
),'EntryDate'); ?></time></p>
          <?php endif; ?>
        
        
          <h2 class="post-title"><a href="<?php echo context::global_filters(dcCore::app()->ctx->posts->getURL(),array (
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
),'EntryURL'); ?>"><?php echo context::global_filters(dcCore::app()->ctx->posts->post_title,array (
  0 => NULL,
  'encode_xml' => 0,
  'encode_html' => '1',
  'cut_string' => 0,
  'lower_case' => 0,
  'upper_case' => 0,
  'encode_url' => 0,
  'remove_html' => 0,
  'capitalize' => 0,
  'strip_tags' => 0,
),'EntryTitle'); ?></a></h2>
        
      </header>
    
    

      <?php if (dcCore::app()->hasBehavior('publicEntryBeforeContent')) { dcCore::app()->callBehavior('publicEntryBeforeContent',dcCore::app(),dcCore::app()->ctx);} ?>
    
    
      
        <?php if(dcCore::app()->ctx->posts->isExtended()) : ?>
          <div class="post-excerpt"><?php echo context::global_filters(dcCore::app()->ctx->posts->getExcerpt(0),array (
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
),'EntryExcerpt'); ?></div>
        <?php endif; ?>
      
      
        <div class="post-content"><?php echo context::global_filters(dcCore::app()->ctx->posts->getContent(0),array (
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
),'EntryContent'); ?></div>
      
    
    

      <?php if (dcCore::app()->hasBehavior('publicEntryAfterContent')) { dcCore::app()->callBehavior('publicEntryAfterContent',dcCore::app(),dcCore::app()->ctx);} ?>
    
    
      <footer class="post-meta">
        
          
            <p class="post-info">
              <span class="post-author"><?php echo __('By'); ?> <?php echo context::global_filters(dcCore::app()->ctx->posts->getAuthorLink(),array (
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
),'EntryAuthorLink'); ?>, </span>
              <span class="post-date"><time datetime="<?php echo context::global_filters(dcCore::app()->ctx->posts->getISO8601Date(''),array (
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
  'iso8601' => '1',
),'EntryDate'); ?>"><?php echo context::global_filters(dcCore::app()->ctx->posts->getDate('',''),array (
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
),'EntryDate'); ?></time>.</span>
              <?php if(dcCore::app()->ctx->posts->cat_id) : ?>
                <span class="post-cat"><?php
dcCore::app()->ctx->categories = dcCore::app()->blog->getCategoryParents(dcCore::app()->ctx->posts->cat_id);
while (dcCore::app()->ctx->categories->fetch()) : ?><a
                href="<?php echo context::global_filters(dcCore::app()->blog->url.dcCore::app()->url->getURLFor("category",dcCore::app()->ctx->categories->cat_url),array (
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
),'CategoryURL'); ?>"><?php echo context::global_filters(dcCore::app()->ctx->categories->cat_title,array (
  0 => NULL,
  'encode_xml' => 0,
  'encode_html' => '1',
  'cut_string' => 0,
  'lower_case' => 0,
  'upper_case' => 0,
  'encode_url' => 0,
  'remove_html' => 0,
  'capitalize' => 0,
  'strip_tags' => 0,
),'CategoryTitle'); ?></a> › <?php endwhile; dcCore::app()->ctx->categories = null; ?><a
                href="<?php echo context::global_filters(dcCore::app()->ctx->posts->getCategoryURL(),array (
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
),'EntryCategoryURL'); ?>"><?php echo context::global_filters(dcCore::app()->ctx->posts->cat_title,array (
  0 => NULL,
  'encode_xml' => 0,
  'encode_html' => '1',
  'cut_string' => 0,
  'lower_case' => 0,
  'upper_case' => 0,
  'encode_url' => 0,
  'remove_html' => 0,
  'capitalize' => 0,
  'strip_tags' => 0,
),'EntryCategory'); ?></a>
              </span>
              <?php endif; ?>
            </p>
          
          
            <?php
dcCore::app()->ctx->meta = dcCore::app()->meta->getMetaRecordset(dcCore::app()->ctx->posts->post_meta,'tag'); dcCore::app()->ctx->meta->sort('meta_id_lower','asc'); ?><?php while (dcCore::app()->ctx->meta->fetch()) : ?>
              <?php if (dcCore::app()->ctx->meta->isStart()) : ?>
                <ul class="post-tags-list">
              <?php endif; ?>
              <li class="post-tags-item"><a href="<?php echo context::global_filters(dcCore::app()->blog->url.dcCore::app()->url->getURLFor("tag",rawurlencode(dcCore::app()->ctx->meta->meta_id)),array (
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
),'TagURL'); ?>"><?php echo context::global_filters(dcCore::app()->ctx->meta->meta_id,array (
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
),'TagID'); ?></a></li>
              <?php if (dcCore::app()->ctx->meta->isEnd()) : ?>
                </ul>
              <?php endif; ?>
            <?php endwhile; dcCore::app()->ctx->meta = null; ?>
          
        
        

          <?php if((dcCore::app()->ctx->posts->hasComments() || dcCore::app()->ctx->posts->commentsActive()) || (dcCore::app()->ctx->posts->hasTrackbacks() || dcCore::app()->ctx->posts->trackbacksActive()) || dcCore::app()->ctx->posts->countMedia('attachment')) : ?>
            <p class="post-info-co">
              <?php if((dcCore::app()->ctx->posts->hasComments() || dcCore::app()->ctx->posts->commentsActive())) : ?>
                <a href="<?php echo context::global_filters(dcCore::app()->ctx->posts->getURL(),array (
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
),'EntryURL'); ?>#comments" class="comment_count"><?php if (dcCore::app()->ctx->posts->nb_comment == 0) {
  printf(__('no comments'),dcCore::app()->ctx->posts->nb_comment);
} elseif (dcCore::app()->ctx->posts->nb_comment == 1) {
  printf(__('one comment'),dcCore::app()->ctx->posts->nb_comment);
} else {
  printf(__('%d comments'),dcCore::app()->ctx->posts->nb_comment);
} ?></a>
              <?php endif; ?>
              <?php if((dcCore::app()->ctx->posts->hasTrackbacks() || dcCore::app()->ctx->posts->trackbacksActive())) : ?>
                <a href="<?php echo context::global_filters(dcCore::app()->ctx->posts->getURL(),array (
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
),'EntryURL'); ?>#pings" class="ping_count"><?php if (dcCore::app()->ctx->posts->nb_trackback == 0) {
  printf(__('no trackbacks'),dcCore::app()->ctx->posts->nb_trackback);
} elseif (dcCore::app()->ctx->posts->nb_trackback == 1) {
  printf(__('one trackback'),dcCore::app()->ctx->posts->nb_trackback);
} else {
  printf(__('%d trackbacks'),dcCore::app()->ctx->posts->nb_trackback);
} ?></a><?php endif; ?>
              <?php if(dcCore::app()->ctx->posts->countMedia('attachment')) : ?>
                <a href="<?php echo context::global_filters(dcCore::app()->ctx->posts->getURL(),array (
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
),'EntryURL'); ?>#attachments" class="attach_count"><?php if (dcCore::app()->ctx->posts->countMedia('attachment') == 0) {
  printf(__('no attachments'),dcCore::app()->ctx->posts->countMedia('attachment'));
} elseif (dcCore::app()->ctx->posts->countMedia('attachment') == 1) {
  printf(__('one attachment'),dcCore::app()->ctx->posts->countMedia('attachment'));
} else {
  printf(__('%d attachments'),dcCore::app()->ctx->posts->countMedia('attachment'));
} ?></a><?php endif; ?>
            </p>
          <?php endif; ?>
        
      </footer>
    
  </article>


