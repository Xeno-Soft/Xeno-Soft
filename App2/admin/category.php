<?php
/**
 * @package Dotclear
 * @subpackage Backend
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
require __DIR__ . '/../inc/admin/prepend.php';

dcPage::check('categories');

$cat_id       = '';
$cat_title    = '';
$cat_url      = '';
$cat_desc     = '';
$cat_position = '';

$blog_settings = new dcSettings(dcCore::app(), dcCore::app()->blog->id);
$blog_lang     = $blog_settings->system->lang;

# Getting existing category
$rs              = null;
$parents         = null;
$cat_parent      = 0;
$siblings        = [];
$allowed_parents = [];

if (!empty($_REQUEST['id'])) {
    try {
        $rs = dcCore::app()->blog->getCategory($_REQUEST['id']);
    } catch (Exception $e) {
        dcCore::app()->error->add($e->getMessage());
    }

    if (!dcCore::app()->error->flag() && !$rs->isEmpty()) {
        $cat_id    = (int) $rs->cat_id;
        $cat_title = $rs->cat_title;
        $cat_url   = $rs->cat_url;
        $cat_desc  = $rs->cat_desc;
    }
    unset($rs);

    # Getting hierarchy information
    $parents    = dcCore::app()->blog->getCategoryParents($cat_id);
    $rs         = dcCore::app()->blog->getCategoryParent($cat_id);
    $cat_parent = $rs->isEmpty() ? 0 : (int) $rs->cat_id;
    unset($rs);

    # Allowed parents list
    $children        = dcCore::app()->blog->getCategories(['start' => $cat_id]);
    $allowed_parents = [__('Top level') => 0];

    $p = [];
    while ($children->fetch()) {
        $p[$children->cat_id] = 1;
    }

    $rs = dcCore::app()->blog->getCategories();
    while ($rs->fetch()) {
        if (!isset($p[$rs->cat_id])) {
            $allowed_parents[] = new formSelectOption(
                str_repeat('&nbsp;&nbsp;', $rs->level - 1) . ($rs->level - 1 == 0 ? '' : '&bull; ') . html::escapeHTML($rs->cat_title),
                $rs->cat_id
            );
        }
    }
    unset($rs);

    # Allowed siblings list
    $rs = dcCore::app()->blog->getCategoryFirstChildren($cat_parent);
    while ($rs->fetch()) {
        if ($rs->cat_id != $cat_id) {
            $siblings[html::escapeHTML($rs->cat_title)] = $rs->cat_id;
        }
    }
    unset($rs);
}

# Changing parent
if ($cat_id && isset($_POST['cat_parent'])) {
    $new_parent = (int) $_POST['cat_parent'];
    if ($cat_parent != $new_parent) {
        try {
            dcCore::app()->blog->setCategoryParent($cat_id, $new_parent);
            dcPage::addSuccessNotice(__('The category has been successfully moved'));
            dcCore::app()->adminurl->redirect('admin.categories');
        } catch (Exception $e) {
            dcCore::app()->error->add($e->getMessage());
        }
    }
}

# Changing sibling
if ($cat_id && isset($_POST['cat_sibling'])) {
    try {
        dcCore::app()->blog->setCategoryPosition($cat_id, (int) $_POST['cat_sibling'], $_POST['cat_move']);
        dcPage::addSuccessNotice(__('The category has been successfully moved'));
        dcCore::app()->adminurl->redirect('admin.categories');
    } catch (Exception $e) {
        dcCore::app()->error->add($e->getMessage());
    }
}

# Create or update a category
if (isset($_POST['cat_title'])) {
    $cur = dcCore::app()->con->openCursor(dcCore::app()->prefix . 'category');

    $cur->cat_title = $cat_title = $_POST['cat_title'];

    if (isset($_POST['cat_desc'])) {
        $cur->cat_desc = $cat_desc = $_POST['cat_desc'];
    }

    if (isset($_POST['cat_url'])) {
        $cur->cat_url = $cat_url = $_POST['cat_url'];
    } else {
        $cur->cat_url = $cat_url;
    }

    try {
        # Update category
        if ($cat_id) {
            # --BEHAVIOR-- adminBeforeCategoryUpdate
            dcCore::app()->callBehavior('adminBeforeCategoryUpdate', $cur, $cat_id);

            dcCore::app()->blog->updCategory($_POST['id'], $cur);

            # --BEHAVIOR-- adminAfterCategoryUpdate
            dcCore::app()->callBehavior('adminAfterCategoryUpdate', $cur, $cat_id);

            dcPage::addSuccessNotice(__('The category has been successfully updated.'));

            dcCore::app()->adminurl->redirect('admin.category', ['id' => $_POST['id']]);
        }
        # Create category
        else {
            # --BEHAVIOR-- adminBeforeCategoryCreate
            dcCore::app()->callBehavior('adminBeforeCategoryCreate', $cur);

            $id = dcCore::app()->blog->addCategory($cur, (int) $_POST['new_cat_parent']);

            # --BEHAVIOR-- adminAfterCategoryCreate
            dcCore::app()->callBehavior('adminAfterCategoryCreate', $cur, $id);

            dcPage::addSuccessNotice(sprintf(
                __('The category "%s" has been successfully created.'),
                html::escapeHTML($cur->cat_title)
            ));
            dcCore::app()->adminurl->redirect('admin.categories');
        }
    } catch (Exception $e) {
        dcCore::app()->error->add($e->getMessage());
    }
}

$title = $cat_id ? html::escapeHTML($cat_title) : __('New category');

$elements = [
    html::escapeHTML(dcCore::app()->blog->name) => '',
    __('Categories')                            => dcCore::app()->adminurl->get('admin.categories'),
];
if ($cat_id) {
    while ($parents->fetch()) {
        $elements[html::escapeHTML($parents->cat_title)] = dcCore::app()->adminurl->get('admin.category', ['id' => $parents->cat_id]);
    }
}
$elements[$title] = '';

$category_editor = dcCore::app()->auth->getOption('editor');
$rte_flag        = true;
$rte_flags       = @dcCore::app()->auth->user_prefs->interface->rte_flags;
if (is_array($rte_flags) && in_array('cat_descr', $rte_flags)) {
    $rte_flag = $rte_flags['cat_descr'];
}

dcPage::open(
    $title,
    dcPage::jsConfirmClose('category-form') .
    dcPage::jsLoad('js/_category.js') .
    ($rte_flag ? dcCore::app()->callBehavior('adminPostEditor', $category_editor['xhtml'], 'category', ['#cat_desc'], 'xhtml') : ''),
    dcPage::breadcrumb($elements)
);

if (!empty($_GET['upd'])) {
    dcPage::success(__('Category has been successfully updated.'));
}

echo
'<form action="' . dcCore::app()->adminurl->get('admin.category') . '" method="post" id="category-form">' .
'<h3>' . __('Category information') . '</h3>' .
'<p><label class="required" for="cat_title"><abbr title="' . __('Required field') . '">*</abbr> ' . __('Name:') . '</label> ' .
form::field('cat_title', 40, 255, [
    'default'    => html::escapeHTML($cat_title),
    'extra_html' => 'required placeholder="' . __('Name') . '" lang="' . $blog_lang . '" spellcheck="true"',
]) .
    '</p>';
if (!$cat_id) {
    $rs = dcCore::app()->blog->getCategories();
    echo
    '<p><label for="new_cat_parent">' . __('Parent:') . ' ' .
    '<select id="new_cat_parent" name="new_cat_parent" >' .
    '<option value="0">' . __('(none)') . '</option>';
    while ($rs->fetch()) {
        echo '<option value="' . $rs->cat_id . '" ' . (!empty($_POST['new_cat_parent']) && $_POST['new_cat_parent'] == $rs->cat_id ? 'selected="selected"' : '') . '>' .
        str_repeat('&nbsp;&nbsp;', $rs->level - 1) . ($rs->level - 1                                                == 0 ? '' : '&bull; ') . html::escapeHTML($rs->cat_title) . '</option>';
    }
    echo
        '</select></label></p>';
    unset($rs);
}
echo
'<div class="lockable">' .
'<p><label for="cat_url">' . __('URL:') . '</label> '
. form::field('cat_url', 40, 255, html::escapeHTML($cat_url)) .
'</p>' .
'<p class="form-note warn" id="note-cat-url">' .
__('Warning: If you set the URL manually, it may conflict with another category.') . '</p>' .
'</div>' .

'<p class="area"><label for="cat_desc">' . __('Description:') . '</label> ' .
form::textarea(
    'cat_desc',
    50,
    8,
    [
        'default'    => html::escapeHTML($cat_desc),
        'extra_html' => 'lang="' . $blog_lang . '" spellcheck="true"',
    ]
) .
'</p>' .

'<p><input type="submit" accesskey="s" value="' . __('Save') . '" />' .
' <input type="button" value="' . __('Cancel') . '" class="go-back reset hidden-if-no-js" />' .
($cat_id ? form::hidden('id', $cat_id) : '') .
dcCore::app()->formNonce() .
    '</p>' .
    '</form>';

if ($cat_id) {
    echo
    '<h3 class="border-top">' . __('Move this category') . '</h3>' .
    '<div class="two-cols">' .
    '<div class="col">' .

    '<form action="' . dcCore::app()->adminurl->get('admin.category') . '" method="post" class="fieldset">' .
    '<h4>' . __('Category parent') . '</h4>' .
    '<p><label for="cat_parent" class="classic">' . __('Parent:') . '</label> ' .
    form::combo('cat_parent', $allowed_parents, $cat_parent) . '</p>' .
    '<p><input type="submit" accesskey="s" value="' . __('Save') . '" />' .
    form::hidden(['id'], $cat_id) . dcCore::app()->formNonce() . '</p>' .
        '</form>' .
        '</div>';

    if (count($siblings) > 0) {
        echo
        '<div class="col">' .
        '<form action="' . dcCore::app()->adminurl->get('admin.category') . '" method="post" class="fieldset">' .
        '<h4>' . __('Category sibling') . '</h4>' .
        '<p><label class="classic" for="cat_sibling">' . __('Move current category') . '</label> ' .
        form::combo(
            'cat_move',
            [__('before') => 'before', __('after') => 'after'],
            ['extra_html' => 'title="' . __('position: ') . '"']
        ) . ' ' .
        form::combo('cat_sibling', $siblings) . '</p>' .
        '<p><input type="submit" accesskey="s" value="' . __('Save') . '" />' .
        form::hidden(['id'], $cat_id) . dcCore::app()->formNonce() . '</p>' .
            '</form>' .
            '</div>';
    }

    echo '</div>';
}

dcPage::helpBlock('core_category');
dcPage::close();
