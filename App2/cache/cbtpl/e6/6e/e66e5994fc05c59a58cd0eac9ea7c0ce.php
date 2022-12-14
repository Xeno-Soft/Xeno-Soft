<!DOCTYPE html>
<html lang="<?php echo context::global_filters(dcCore::app()->blog->settings->system->lang,array (
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
),'BlogLanguage'); ?>">

<head>
  
    <meta charset="UTF-8" />
    
      <title><?php echo context::global_filters(dcCore::app()->blog->name,array (
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
),'BlogName'); ?><?php if(!context::PaginationStart()) : ?> - <?php echo __('page'); ?> <?php echo context::global_filters(context::PaginationPosition(0),array (
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
),'PaginationCurrent'); ?><?php endif; ?>
      </title>
     
    
      <meta name="copyright" content="<?php echo context::global_filters(dcCore::app()->blog->settings->system->copyright_notice,array (
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
),'BlogCopyrightNotice'); ?>" />
      
        <meta name="ROBOTS" content="<?php echo context::robotsPolicy(dcCore::app()->blog->settings->system->robots_policy,''); ?>" />
       
      
        <meta name="description" lang="<?php echo context::global_filters(dcCore::app()->blog->settings->system->lang,array (
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
),'BlogLanguage'); ?>" content="<?php echo context::global_filters(dcCore::app()->blog->desc,array (
  0 => NULL,
  'encode_xml' => 0,
  'encode_html' => '1',
  'cut_string' => '180',
  'lower_case' => 0,
  'upper_case' => 0,
  'encode_url' => 0,
  'remove_html' => '1',
  'capitalize' => 0,
  'strip_tags' => 0,
),'BlogDescription'); ?><?php if(context::PaginationStart()) : ?> - <?php echo __('page'); ?> <?php echo context::global_filters(context::PaginationPosition(0),array (
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
),'PaginationCurrent'); ?><?php endif; ?>" />
        <meta name="author" content="<?php echo context::global_filters(dcCore::app()->blog->settings->system->editor,array (
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
),'BlogEditor'); ?>" />
        <meta name="date" content="<?php echo context::global_filters(dt::iso8601(dcCore::app()->blog->upddt,dcCore::app()->blog->settings->system->blog_timezone),array (
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
),'BlogUpdateDate'); ?>" />
       
     
    
      <link rel="contents" title="<?php echo __('Archives'); ?>" href="<?php echo context::global_filters(dcCore::app()->blog->url.dcCore::app()->url->getURLFor("archive"),array (
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
),'BlogArchiveURL'); ?>" />
      <?php
if (!isset($params)) $params = [];
dcCore::app()->ctx->categories = dcCore::app()->blog->getCategories($params);
?>
<?php while (dcCore::app()->ctx->categories->fetch()) : ?>
        <link rel="section" href="<?php echo context::global_filters(dcCore::app()->blog->url.dcCore::app()->url->getURLFor("category",dcCore::app()->ctx->categories->cat_url),array (
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
),'CategoryURL'); ?>" title="<?php echo context::global_filters(dcCore::app()->ctx->categories->cat_title,array (
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
),'CategoryTitle'); ?>" />
      <?php endwhile; dcCore::app()->ctx->categories = null; unset($params); ?>
      <?php if (dcCore::app()->ctx->exists("meta") && dcCore::app()->ctx->meta->rows() && (dcCore::app()->ctx->meta->meta_type == "tag")) { if (!isset($params)) { $params = []; }
if (!isset($params['from'])) { $params['from'] = ''; }
if (!isset($params['sql'])) { $params['sql'] = ''; }
$params['from'] .= ', '.dcCore::app()->prefix.'meta META ';
$params['sql'] .= 'AND META.post_id = P.post_id ';
$params['sql'] .= "AND META.meta_type = 'tag' ";
$params['sql'] .= "AND META.meta_id = '".dcCore::app()->con->escape(dcCore::app()->ctx->meta->meta_id)."' ";
} ?>
<?php
if (!isset($_page_number)) { $_page_number = 1; }
$nb_entry_first_page=dcCore::app()->ctx->nb_entry_first_page; $nb_entry_per_page = dcCore::app()->ctx->nb_entry_per_page;
if ((dcCore::app()->url->type == 'default') || (dcCore::app()->url->type == 'default-page')) {
    $params['limit'] = ($_page_number == 1 ? $nb_entry_first_page : $nb_entry_per_page);
} else {
    $params['limit'] = $nb_entry_per_page;
}
if ((dcCore::app()->url->type == 'default') || (dcCore::app()->url->type == 'default-page')) {
    $params['limit'] = [($_page_number == 1 ? 0 : ($_page_number - 2) * $nb_entry_per_page + $nb_entry_first_page),$params['limit']];
} else {
    $params['limit'] = [($_page_number - 1) * $nb_entry_per_page,$params['limit']];
}
if (dcCore::app()->ctx->exists("users")) { $params['user_id'] = dcCore::app()->ctx->users->user_id; }
if (dcCore::app()->ctx->exists("categories")) { $params['cat_id'] = dcCore::app()->ctx->categories->cat_id.(dcCore::app()->blog->settings->system->inc_subcats?' ?sub':'');}
if (dcCore::app()->ctx->exists("archives")) { $params['post_year'] = dcCore::app()->ctx->archives->year(); $params['post_month'] = dcCore::app()->ctx->archives->month(); unset($params['limit']); }
if (dcCore::app()->ctx->exists("langs")) { $params['post_lang'] = dcCore::app()->ctx->langs->post_lang; }
if (isset($_search)) { $params['search'] = $_search; }
$params['order'] = 'post_dt desc';
$params['no_content'] = true;
dcCore::app()->ctx->post_params = $params;
dcCore::app()->ctx->posts = dcCore::app()->blog->getPosts($params); unset($params);
?>
<?php while (dcCore::app()->ctx->posts->fetch()) : ?>
        <?php if (dcCore::app()->ctx->posts->isStart()) : ?>
          <?php
$params = dcCore::app()->ctx->post_params;
dcCore::app()->ctx->pagination = dcCore::app()->blog->getPosts($params,true); unset($params);
?>
<?php if (dcCore::app()->ctx->pagination->f(0) > dcCore::app()->ctx->posts->count()) : ?>
            <?php if(!context::PaginationEnd()) : ?>
              <link rel="prev" title="<?php echo __('previous entries'); ?>" href="<?php echo context::global_filters(context::PaginationURL(1),array (
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
  'offset' => '1',
),'PaginationURL'); ?>" />
            <?php endif; ?>
            <?php if(!context::PaginationStart()) : ?>
              <link rel="next" title="<?php echo __('next entries'); ?>" href="<?php echo context::global_filters(context::PaginationURL(-1),array (
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
),'PaginationURL'); ?>" />
            <?php endif; ?>
          <?php endif; ?>
        <?php endif; ?>
        <link rel="chapter" href="<?php echo context::global_filters(dcCore::app()->ctx->posts->getURL(),array (
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
),'EntryURL'); ?>" title="<?php echo context::global_filters(dcCore::app()->ctx->posts->post_title,array (
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
),'EntryTitle'); ?>" />
      <?php endwhile; dcCore::app()->ctx->posts = null; dcCore::app()->ctx->post_params = null; ?>
      <link rel="alternate" type="application/atom+xml" title="Atom 1.0" href="<?php echo context::global_filters(dcCore::app()->blog->url.dcCore::app()->url->getURLFor("feed","atom"),array (
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
  'type' => 'atom',
),'BlogFeedURL'); ?>" />
      <link rel="EditURI" type="application/rsd+xml" title="RSD" href="<?php echo context::global_filters(dcCore::app()->blog->url.dcCore::app()->url->getURLFor('rsd'),array (
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
),'BlogRSDURL'); ?>" />
      <link rel="meta" type="application/xbel+xml" title="Blogroll" href="<?php echo context::global_filters(dcCore::app()->blog->url.dcCore::app()->url->getURLFor("xbel"),array (
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
),'BlogrollXbelLink'); ?>" />
     
    <?php try { echo dcCore::app()->tpl->getData('_head.html'); } catch (Exception $e) {} ?>

   
</head>


  <body class="dc-home <?php if(dcCore::app()->url->type == 'default') : ?>dc-home-first<?php endif; ?>">



  <div id="page">
    
      
        <?php try { echo dcCore::app()->tpl->getData('_top.html'); } catch (Exception $e) {} ?>

       
      <div id="wrapper">
        
          <main id="main" role="main">
            
              
                <?php echo tplBreadcrumb::displayBreadcrumb(''); ?>
              
              <section id="content">
                
                  
                  <?php if (dcCore::app()->ctx->exists("meta") && dcCore::app()->ctx->meta->rows() && (dcCore::app()->ctx->meta->meta_type == "tag")) { if (!isset($params)) { $params = []; }
if (!isset($params['from'])) { $params['from'] = ''; }
if (!isset($params['sql'])) { $params['sql'] = ''; }
$params['from'] .= ', '.dcCore::app()->prefix.'meta META ';
$params['sql'] .= 'AND META.post_id = P.post_id ';
$params['sql'] .= "AND META.meta_type = 'tag' ";
$params['sql'] .= "AND META.meta_id = '".dcCore::app()->con->escape(dcCore::app()->ctx->meta->meta_id)."' ";
} ?>
<?php
if (!isset($_page_number)) { $_page_number = 1; }
$nb_entry_first_page=dcCore::app()->ctx->nb_entry_first_page; $nb_entry_per_page = dcCore::app()->ctx->nb_entry_per_page;
if ((dcCore::app()->url->type == 'default') || (dcCore::app()->url->type == 'default-page')) {
    $params['limit'] = ($_page_number == 1 ? $nb_entry_first_page : $nb_entry_per_page);
} else {
    $params['limit'] = $nb_entry_per_page;
}
if ((dcCore::app()->url->type == 'default') || (dcCore::app()->url->type == 'default-page')) {
    $params['limit'] = [($_page_number == 1 ? 0 : ($_page_number - 2) * $nb_entry_per_page + $nb_entry_first_page),$params['limit']];
} else {
    $params['limit'] = [($_page_number - 1) * $nb_entry_per_page,$params['limit']];
}
if (dcCore::app()->ctx->exists("users")) { $params['user_id'] = dcCore::app()->ctx->users->user_id; }
if (dcCore::app()->ctx->exists("categories")) { $params['cat_id'] = dcCore::app()->ctx->categories->cat_id.(dcCore::app()->blog->settings->system->inc_subcats?' ?sub':'');}
if (dcCore::app()->ctx->exists("archives")) { $params['post_year'] = dcCore::app()->ctx->archives->year(); $params['post_month'] = dcCore::app()->ctx->archives->month(); unset($params['limit']); }
if (dcCore::app()->ctx->exists("langs")) { $params['post_lang'] = dcCore::app()->ctx->langs->post_lang; }
if (isset($_search)) { $params['search'] = $_search; }
$params['order'] = 'post_dt desc';
dcCore::app()->ctx->post_params = $params;
dcCore::app()->ctx->posts = dcCore::app()->blog->getPosts($params); unset($params);
?>
<?php while (dcCore::app()->ctx->posts->fetch()) : ?>

                    <?php if(dcCore::app()->url->type == 'default') : ?>
                      <?php if (dcCore::app()->ctx->loopPosition(0,1,null,null)) : ?>
                        <?php try { echo dcCore::app()->tpl->getData('_entry-full.html'); } catch (Exception $e) {} ?>

                      <?php endif; ?>
                      <?php if (dcCore::app()->ctx->loopPosition(1,null,null,null)) : ?>
                        <?php try { echo dcCore::app()->tpl->getData('_entry-short.html'); } catch (Exception $e) {} ?>

                      <?php endif; ?>
                    <?php endif; ?>

                    <?php if(dcCore::app()->url->type != 'default') : ?>
                      <?php try { echo dcCore::app()->tpl->getData('_entry-short.html'); } catch (Exception $e) {} ?>

                    <?php endif; ?>

                    <?php if (dcCore::app()->ctx->posts->isEnd()) : ?>
                      <?php try { echo dcCore::app()->tpl->getData('_pagination.html'); } catch (Exception $e) {} ?>

                    <?php endif; ?>
                  <?php endwhile; dcCore::app()->ctx->posts = null; dcCore::app()->ctx->post_params = null; ?>
                  
                 
              </section> 
             
          </main> 
          
            <?php try { echo dcCore::app()->tpl->getData('_sidebar.html'); } catch (Exception $e) {} ?>

           
         
      </div> 
      
        <?php try { echo dcCore::app()->tpl->getData('_footer.html'); } catch (Exception $e) {} ?>

       
     
  </div> 
 
</body>

</html>
