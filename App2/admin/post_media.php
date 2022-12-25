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

$post_id   = !empty($_REQUEST['post_id']) ? (int) $_REQUEST['post_id'] : null;
$media_id  = !empty($_REQUEST['media_id']) ? (int) $_REQUEST['media_id'] : null;
$link_type = !empty($_REQUEST['link_type']) ? $_REQUEST['link_type'] : null;

if (!$post_id) {
    exit;
}
$rs = dcCore::app()->blog->getPosts(['post_id' => $post_id, 'post_type' => '']);
if ($rs->isEmpty()) {
    exit;
}

try {
    if ($media_id && !empty($_REQUEST['attach'])) {
        $pm = new dcPostMedia(dcCore::app());
        $pm->addPostMedia($post_id, $media_id, $link_type);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            header('Content-type: application/json');
            echo json_encode(['url' => dcCore::app()->getPostAdminURL($rs->post_type, $post_id, false)]);
            exit();
        }
        http::redirect(dcCore::app()->getPostAdminURL($rs->post_type, $post_id, false));
    }

    dcCore::app()->media = new dcMedia(dcCore::app());
    $f                   = dcCore::app()->media->getPostMedia($post_id, $media_id, $link_type);
    if (empty($f)) {
        $post_id = $media_id = null;

        throw new Exception(__('This attachment does not exist'));
    }
    $f = $f[0];
} catch (Exception $e) {
    dcCore::app()->error->add($e->getMessage());
}

# Remove a media from en
if (($post_id && $media_id) || dcCore::app()->error->flag()) {
    if (!empty($_POST['remove'])) {
        $pm = new dcPostMedia(dcCore::app());
        $pm->removePostMedia($post_id, $media_id, $link_type);

        dcPage::addSuccessNotice(__('Attachment has been successfully removed.'));
        http::redirect(dcCore::app()->getPostAdminURL($rs->post_type, $post_id, false));
    } elseif (isset($_POST['post_id'])) {
        http::redirect(dcCore::app()->getPostAdminURL($rs->post_type, $post_id, false));
    }

    if (!empty($_GET['remove'])) {
        dcPage::open(__('Remove attachment'));

        echo '<h2>' . __('Attachment') . ' &rsaquo; <span class="page-title">' . __('confirm removal') . '</span></h2>';

        echo
        '<form action="' . dcCore::app()->adminurl->get('admin.post.media') . '" method="post">' .
        '<p>' . __('Are you sure you want to remove this attachment?') . '</p>' .
        '<p><input type="submit" class="reset" value="' . __('Cancel') . '" /> ' .
        ' &nbsp; <input type="submit" class="delete" name="remove" value="' . __('Yes') . '" />' .
        form::hidden('post_id', $post_id) .
        form::hidden('media_id', $media_id) .
        dcCore::app()->formNonce() . '</p>' .
            '</form>';

        dcPage::close();
        exit;
    }
}
