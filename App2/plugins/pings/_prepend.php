<?php
/**
 * @brief pings, a plugin for Dotclear 2
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

$__autoload['pingsAPI']           = __DIR__ . '/lib.pings.php';
$__autoload['pingsCoreBehaviour'] = __DIR__ . '/lib.pings.php';

dcCore::app()->addBehavior('coreFirstPublicationEntries', ['pingsCoreBehaviour', 'doPings']);
