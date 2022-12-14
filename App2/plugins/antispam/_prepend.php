<?php
/**
 * @brief antispam, a plugin for Dotclear 2
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

$__autoload['dcSpamFilter']  = __DIR__ . '/inc/class.dc.spamfilter.php';
$__autoload['dcSpamFilters'] = __DIR__ . '/inc/class.dc.spamfilters.php';
$__autoload['dcAntispam']    = __DIR__ . '/inc/lib.dc.antispam.php';
$__autoload['dcAntispamURL'] = __DIR__ . '/inc/lib.dc.antispam.url.php';

$__autoload['dcFilterIP']          = __DIR__ . '/filters/class.dc.filter.ip.php';
$__autoload['dcFilterIPv6']        = __DIR__ . '/filters/class.dc.filter.ipv6.php';
$__autoload['dcFilterIpLookup']    = __DIR__ . '/filters/class.dc.filter.iplookup.php';
$__autoload['dcFilterLinksLookup'] = __DIR__ . '/filters/class.dc.filter.linkslookup.php';
$__autoload['dcFilterWords']       = __DIR__ . '/filters/class.dc.filter.words.php';

dcCore::app()->spamfilters = ['dcFilterIP', 'dcFilterIpLookup', 'dcFilterWords', 'dcFilterLinksLookup'];

// IP v6 filter depends on some math libraries, so enable it only if one of them is available
if (function_exists('gmp_init') || function_exists('bcadd')) {
    dcCore::app()->spamfilters[] = 'dcFilterIPv6';
}

dcCore::app()->url->register('spamfeed', 'spamfeed', '^spamfeed/(.+)$', ['dcAntispamURL', 'spamFeed']);
dcCore::app()->url->register('hamfeed', 'hamfeed', '^hamfeed/(.+)$', ['dcAntispamURL', 'hamFeed']);

if (!defined('DC_CONTEXT_ADMIN')) {
    return false;
}

// Admin mode

$__autoload['dcAntispamRest'] = __DIR__ . '/_services.php';

// Register REST methods
dcCore::app()->rest->addFunction('getSpamsCount', ['dcAntispamRest', 'getSpamsCount']);
