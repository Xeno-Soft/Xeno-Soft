<?php
/**
 * @package Dotclear
 * @subpackage Backend
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 *
 * @deprecated It is only used for plugins compatibility
 */
require __DIR__ . '/../inc/admin/prepend.php';

dcPage::check('usage,contentadmin');

if (isset($_REQUEST['redir'])) {
    $u   = explode('?', $_REQUEST['redir']);
    $uri = $u[0];
    if (isset($u[1])) {
        parse_str($u[1], $args);
    }
    $args['redir'] = $_REQUEST['redir'];
} else {
    $uri  = dcCore::app()->adminurl->get('admin.comments');
    $args = [];
}

$comments_actions_page = new dcCommentsActionsPage(dcCore::app(), $uri, $args);
$comments_actions_page->setEnableRedirSelection(false);
$comments_actions_page->process();
