<?php
/**
 * @package Dotclear
 * @subpackage Backend
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
require __DIR__ . '/../inc/admin/prepend.php';

dcPage::check('usage,contentadmin');

$href      = !empty($_GET['href']) ? $_GET['href'] : '';
$hreflang  = !empty($_GET['hreflang']) ? $_GET['hreflang'] : '';
$title     = !empty($_GET['title']) ? $_GET['title'] : '';
$plugin_id = !empty($_GET['plugin_id']) ? html::sanitizeURL($_GET['plugin_id']) : '';

if (dcCore::app()->themes === null) {
    # -- Loading themes, may be useful for some configurable theme --
    dcCore::app()->themes = new dcThemes(dcCore::app());
    dcCore::app()->themes->loadModules(dcCore::app()->blog->themes_path, null);
}

dcPage::openPopup(__('Add a link'), dcPage::jsLoad('js/_popup_link.js') . dcCore::app()->callBehavior('adminPopupLink', $plugin_id));

echo '<h2 class="page-title">' . __('Add a link') . '</h2>';

# Languages combo
$rs         = dcCore::app()->blog->getLangs(['order' => 'asc']);
$lang_combo = dcAdminCombos::getLangsCombo($rs, true);

echo
'<form id="link-insert-form" action="#" method="get">' .
'<p><label class="required" for="href"><abbr title="' . __('Required field') . '">*</abbr> ' . __('Link URL:') . '</label> ' .
form::field('href', 35, 512, [
    'default'    => html::escapeHTML($href),
    'extra_html' => 'required placeholder="' . __('URL') . '"',
]) .
'</p>' .
'<p><label for="title">' . __('Link title:') . '</label> ' .
form::field('title', 35, 512, html::escapeHTML($title)) . '</p>' .
'<p><label for="hreflang">' . __('Link language:') . '</label> ' .
form::combo('hreflang', $lang_combo, $hreflang) .
'</p>' .

'</form>' .

'<p><button type="button" class="reset" id="link-insert-cancel">' . __('Cancel') . '</button> - ' .
'<button type="button" id="link-insert-ok"><strong>' . __('Insert') . '</strong></button></p>' . "\n";

dcPage::closePopup();
