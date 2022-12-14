<?php
/**
 * @brief pages, a plugin for Dotclear 2
 *
 * @package Dotclear
 * @subpackage Plugins
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

$act = !empty($_REQUEST['act']) ? $_REQUEST['act'] : 'list';

if ($act == 'page') {
    include __DIR__ . '/page.php';
} else {
    include __DIR__ . '/list.php';
}
