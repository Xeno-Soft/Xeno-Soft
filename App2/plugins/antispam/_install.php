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
if (!defined('DC_CONTEXT_ADMIN')) {
    return;
}

$version = dcCore::app()->plugins->moduleInfo('antispam', 'version');
if (version_compare(dcCore::app()->getVersion('antispam'), $version, '>=')) {
    return;
}

/* Database schema
-------------------------------------------------------- */
$s = new dbStruct(dcCore::app()->con, dcCore::app()->prefix);

$s->spamrule
    ->rule_id('bigint', 0, false)
    ->blog_id('varchar', 32, true)
    ->rule_type('varchar', 16, false, "'word'")
    ->rule_content('varchar', 128, false)

    ->primary('pk_spamrule', 'rule_id')
;

$s->spamrule->index('idx_spamrule_blog_id', 'btree', 'blog_id');
$s->spamrule->reference('fk_spamrule_blog', 'blog_id', 'blog', 'blog_id', 'cascade', 'cascade');

if ($s->driver() == 'pgsql') {
    $s->spamrule->index('idx_spamrule_blog_id_null', 'btree', '(blog_id IS NULL)');
}

# Schema installation
$si      = new dbStruct(dcCore::app()->con, dcCore::app()->prefix);
$changes = $si->synchronize($s);

# Creating default wordslist
if (dcCore::app()->getVersion('antispam') === null) {
    $_o = new dcFilterWords(dcCore::app());
    $_o->defaultWordsList();
    unset($_o);
}

dcCore::app()->blog->settings->addNamespace('antispam');
dcCore::app()->blog->settings->antispam->put('antispam_moderation_ttl', 0, 'integer', 'Antispam Moderation TTL (days)', false);

dcCore::app()->setVersion('antispam', $version);

return true;
