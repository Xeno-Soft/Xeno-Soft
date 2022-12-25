<?php
/**
 * @brief breadcrumb, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
if (!defined('DC_RC_PATH')) {
    return;
}

# Breadcrumb template functions
dcCore::app()->tpl->addValue('Breadcrumb', ['tplBreadcrumb', 'breadcrumb']);

class tplBreadcrumb
{
    # Template function
    public static function breadcrumb($attr)
    {
        $separator = $attr['separator'] ?? '';

        return '<?php echo tplBreadcrumb::displayBreadcrumb(' .
        "'" . addslashes($separator) . "'" .
            '); ?>';
    }

    public static function displayBreadcrumb($separator)
    {
        $ret = '';

        # Check if breadcrumb enabled for the current blog
        dcCore::app()->blog->settings->addNameSpace('breadcrumb');
        if (!dcCore::app()->blog->settings->breadcrumb->breadcrumb_enabled) {
            return $ret;
        }

        if ($separator == '') {
            $separator = ' &rsaquo; ';
        }

        // Get current page if set
        $page = isset($GLOBALS['_page_number']) ? (int) $GLOBALS['_page_number'] : 0;

        switch (dcCore::app()->url->type) {

            case 'static':
                // Static home
                $ret = '<span id="bc-home">' . __('Home') . '</span>';

                break;

            case 'default':
                if (dcCore::app()->blog->settings->system->static_home) {
                    // Static home and on (1st) blog page
                    $ret = '<a id="bc-home" href="' . dcCore::app()->blog->url . '">' . __('Home') . '</a>';
                    $ret .= $separator . __('Blog');
                } else {
                    // Home (first page only)
                    $ret = '<span id="bc-home">' . __('Home') . '</span>';
                    if (dcCore::app()->ctx->cur_lang) {
                        $langs = l10n::getISOCodes();
                        $ret .= $separator . ($langs[dcCore::app()->ctx->cur_lang] ?? dcCore::app()->ctx->cur_lang);
                    }
                }

                break;

            case 'default-page':
                // Home or blog page`(page 2 to n)
                $ret = '<a id="bc-home" href="' . dcCore::app()->blog->url . '">' . __('Home') . '</a>';
                if (dcCore::app()->blog->settings->system->static_home) {
                    $ret .= $separator . '<a href="' . dcCore::app()->blog->url . dcCore::app()->url->getURLFor('posts') . '">' . __('Blog') . '</a>';
                } else {
                    if (dcCore::app()->ctx->cur_lang) {
                        $langs = l10n::getISOCodes();
                        $ret .= $separator . ($langs[dcCore::app()->ctx->cur_lang] ?? dcCore::app()->ctx->cur_lang);
                    }
                }
                $ret .= $separator . sprintf(__('page %d'), $page);

                break;

            case 'category':
                // Category
                $ret        = '<a id="bc-home" href="' . dcCore::app()->blog->url . '">' . __('Home') . '</a>';
                $categories = dcCore::app()->blog->getCategoryParents(dcCore::app()->ctx->categories->cat_id);
                while ($categories->fetch()) {
                    $ret .= $separator . '<a href="' . dcCore::app()->blog->url . dcCore::app()->url->getURLFor('category', $categories->cat_url) . '">' . $categories->cat_title . '</a>';
                }
                if ($page == 0) {
                    $ret .= $separator . dcCore::app()->ctx->categories->cat_title;
                } else {
                    $ret .= $separator . '<a href="' . dcCore::app()->blog->url . dcCore::app()->url->getURLFor('category', dcCore::app()->ctx->categories->cat_url) . '">' . dcCore::app()->ctx->categories->cat_title . '</a>';
                    $ret .= $separator . sprintf(__('page %d'), $page);
                }

                break;

            case 'post':
                // Post
                $ret = '<a id="bc-home" href="' . dcCore::app()->blog->url . '">' . __('Home') . '</a>';
                if (dcCore::app()->ctx->posts->cat_id) {
                    // Parents cats of post's cat
                    $categories = dcCore::app()->blog->getCategoryParents(dcCore::app()->ctx->posts->cat_id);
                    while ($categories->fetch()) {
                        $ret .= $separator . '<a href="' . dcCore::app()->blog->url . dcCore::app()->url->getURLFor('category', $categories->cat_url) . '">' . $categories->cat_title . '</a>';
                    }
                    // Post's cat
                    $categories = dcCore::app()->blog->getCategory(dcCore::app()->ctx->posts->cat_id);
                    $ret .= $separator . '<a href="' . dcCore::app()->blog->url . dcCore::app()->url->getURLFor('category', $categories->cat_url) . '">' . $categories->cat_title . '</a>';
                }
                $ret .= $separator . dcCore::app()->ctx->posts->post_title;

                break;

            case 'lang':
                // Lang
                $ret   = '<a id="bc-home" href="' . dcCore::app()->blog->url . '">' . __('Home') . '</a>';
                $langs = l10n::getISOCodes();
                $ret .= $separator . ($langs[dcCore::app()->ctx->cur_lang] ?? dcCore::app()->ctx->cur_lang);

                break;

            case 'archive':
                // Archives
                $ret = '<a id="bc-home" href="' . dcCore::app()->blog->url . '">' . __('Home') . '</a>';
                if (!dcCore::app()->ctx->archives) {
                    // Global archives
                    $ret .= $separator . __('Archives');
                } else {
                    // Month archive
                    $ret .= $separator . '<a href="' . dcCore::app()->blog->url . dcCore::app()->url->getURLFor('archive') . '">' . __('Archives') . '</a>';
                    $ret .= $separator . dt::dt2str('%B %Y', dcCore::app()->ctx->archives->dt);
                }

                break;

            case 'pages':
                // Page
                $ret = '<a id="bc-home" href="' . dcCore::app()->blog->url . '">' . __('Home') . '</a>';
                $ret .= $separator . dcCore::app()->ctx->posts->post_title;

                break;

            case 'tags':
                // All tags
                $ret = '<a id="bc-home" href="' . dcCore::app()->blog->url . '">' . __('Home') . '</a>';
                $ret .= $separator . __('All tags');

                break;

            case 'tag':
                // Tag
                $ret = '<a id="bc-home" href="' . dcCore::app()->blog->url . '">' . __('Home') . '</a>';
                $ret .= $separator . '<a href="' . dcCore::app()->blog->url . dcCore::app()->url->getURLFor('tags') . '">' . __('All tags') . '</a>';
                if ($page == 0) {
                    $ret .= $separator . dcCore::app()->ctx->meta->meta_id;
                } else {
                    $ret .= $separator . '<a href="' . dcCore::app()->blog->url . dcCore::app()->url->getURLFor('tag', rawurlencode(dcCore::app()->ctx->meta->meta_id)) . '">' . dcCore::app()->ctx->meta->meta_id . '</a>';
                    $ret .= $separator . sprintf(__('page %d'), $page);
                }

                break;

            case 'search':
                // Search
                $ret = '<a id="bc-home" href="' . dcCore::app()->blog->url . '">' . __('Home') . '</a>';
                if ($page == 0) {
                    $ret .= $separator . __('Search:') . ' ' . $GLOBALS['_search'];
                } else {
                    $ret .= $separator . '<a href="' . dcCore::app()->blog->url . '?q=' . rawurlencode($GLOBALS['_search']) . '">' . __('Search:') . ' ' . $GLOBALS['_search'] . '</a>';
                    $ret .= $separator . sprintf(__('page %d'), $page);
                }

                break;

            case '404':
                // 404
                $ret = '<a id="bc-home" href="' . dcCore::app()->blog->url . '">' . __('Home') . '</a>';
                $ret .= $separator . __('404');

                break;

            default:
                $ret = '<a id="bc-home" href="' . dcCore::app()->blog->url . '">' . __('Home') . '</a>';
                # --BEHAVIOR-- publicBreadcrumb
                # Should specific breadcrumb if any, will be added after home page url
                $special = dcCore::app()->callBehavior('publicBreadcrumb', dcCore::app()->url->type, $separator);
                if ($special) {
                    $ret .= $separator . $special;
                }

                break;
        }

        # Encapsulate breadcrumb in <p>…</p>
        if (!dcCore::app()->blog->settings->breadcrumb->breadcrumb_alone) {
            $ret = '<p id="breadcrumb">' . $ret . '</p>';
        }

        return $ret;
    }
}
