<?php
/**
 * @package Dotclear
 * @subpackage Backend
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
if (!defined('DC_RC_PATH')) {
    return;
}

/**
 * dcNotices -- Backend notices handling facilities
 */
class dcAdminNotices
{
    private static $N_TYPES = [
        // id → CSS class
        'success' => 'success',
        'warning' => 'warning-msg',
        'error'   => 'error',
        'message' => 'message',
        'static'  => 'static-msg', ];

    private static $error_displayed = false;

    /**
     * Gets the HTML code of notices.
     *
     * @return     string  The notices.
     */
    public static function getNotices()
    {
        // Update transition from 2.22 to 2.23
        if (version_compare(DC_VERSION, '2.23', '<')) {
            global $core;
        } else {
            $core = dcCore::app();
        }

        $res = '';

        // return error messages if any
        if ($core->error->flag() && !self::$error_displayed) {

            # --BEHAVIOR-- adminPageNotificationError
            $notice_error = $core->callBehavior('adminPageNotificationError', $core, $core->error);

            if (isset($notice_error) && !empty($notice_error)) {
                $res .= $notice_error;
            } else {
                $res .= '<div class="error" role="alert"><p>' .
                '<strong>' . (count($core->error->getErrors()) > 1 ? __('Errors:') : __('Error:')) . '</strong>' .
                '</p>' . $core->error->toHTML() . '</div>';
            }
            self::$error_displayed = true;
        } else {
            self::$error_displayed = false;
        }

        // return notices if any

        // Should retrieve static notices first, then others
        $step = 2;
        do {
            if ($step == 2) {
                // Static notifications
                $params = [
                    'notice_type' => 'static',
                ];
            } else {
                // Normal notifications
                $params = [
                    'sql' => "AND notice_type != 'static'",
                ];
            }
            $counter = $core->notices->getNotices($params, true);
            if ($counter) {
                $lines = $core->notices->getNotices($params);
                while ($lines->fetch()) {
                    if (isset(self::$N_TYPES[$lines->notice_type])) {
                        $class = self::$N_TYPES[$lines->notice_type];
                    } else {
                        $class = $lines->notice_type;
                    }
                    $notification = [
                        'type'   => $lines->notice_type,
                        'class'  => $class,
                        'ts'     => $lines->notice_ts,
                        'text'   => $lines->notice_msg,
                        'format' => $lines->notice_format,
                    ];
                    if ($lines->notice_options !== null) {
                        $notification = array_merge($notification, @json_decode($lines->notice_options, true));
                    }
                    # --BEHAVIOR-- adminPageNotification
                    $notice = $core->callBehavior('adminPageNotification', $core, $notification);

                    $res .= (isset($notice) && !empty($notice) ? $notice : self::getNotification($notification));
                }
            }
        } while (--$step);

        // Delete returned notices
        $core->notices->delNotices(null, true);

        return $res;
    }

    /**
     * Adds a notice.
     *
     * @param      string  $type     The type
     * @param      string  $message  The message
     * @param      array   $options  The options
     */
    public static function addNotice($type, $message, $options = [])
    {
        $cur = dcCore::app()->con->openCursor(dcCore::app()->prefix . dcCore::app()->notices->getTable());

        $now = function () {
            dt::setTZ(dcCore::app()->auth->getInfo('user_tz')); // Set user TZ
            $dt = date('Y-m-d H:i:s');
            dt::setTZ('UTC');                           // Back to default TZ

            return $dt;
        };

        $cur->notice_type    = $type;
        $cur->notice_ts      = isset($options['ts']) && $options['ts'] ? $options['ts'] : $now();
        $cur->notice_msg     = $message;
        $cur->notice_options = json_encode($options);

        if (isset($options['divtag']) && $options['divtag']) {
            $cur->notice_format = 'html';
        }
        if (isset($options['format']) && $options['format']) {
            $cur->notice_format = $options['format'];
        }

        dcCore::app()->notices->addNotice($cur);
    }

    /**
     * Adds a success notice.
     *
     * @param      string  $message  The message
     * @param      array   $options  The options
     */
    public static function addSuccessNotice($message, $options = [])
    {
        self::addNotice('success', $message, $options);
    }

    /**
     * Adds a warning notice.
     *
     * @param      string  $message  The message
     * @param      array   $options  The options
     */
    public static function addWarningNotice($message, $options = [])
    {
        self::addNotice('warning', $message, $options);
    }

    /**
     * Adds an error notice.
     *
     * @param      string  $message  The message
     * @param      array   $options  The options
     */
    public static function addErrorNotice($message, $options = [])
    {
        self::addNotice('error', $message, $options);
    }

    /**
     * Gets the notification.
     *
     * @param      array  $n      The notification
     *
     * @return     string  The notification.
     */
    private static function getNotification($n)
    {
        // Update transition from 2.22 to 2.23
        if (version_compare(DC_VERSION, '2.23', '<')) {
            global $core;
        } else {
            $core = dcCore::app();
        }

        $tag = (isset($n['format']) && $n['format'] === 'html') ? 'div' : 'p';
        $ts  = '';
        if (!isset($n['with_ts']) || ($n['with_ts'] == true)) {
            $ts = '<span class="notice-ts">' .
                '<time datetime="' . dt::iso8601(strtotime($n['ts']), $core->auth->getInfo('user_tz')) . '">' .
                dt::dt2str(__('%H:%M:%S'), $n['ts'], $core->auth->getInfo('user_tz')) .
                '</time>' .
                '</span> ';
        }
        $res = '<' . $tag . ' class="' . $n['class'] . '" role="alert">' . $ts . $n['text'] . '</' . $tag . '>';

        return $res;
    }

    /*  */

    /**
     * Direct messages, usually immediately displayed
     *
     * @param      string  $msg        The message
     * @param      bool    $timestamp  With the timestamp
     * @param      bool    $div        Inside a div (else in a p)
     * @param      bool    $echo       Display the message?
     * @param      string  $class      The class of block (div/p)
     *
     * @return     string
     */
    public static function message($msg, $timestamp = true, $div = false, $echo = true, $class = 'message')
    {
        $res = '';
        if ($msg != '') {
            $ts = '';
            if ($timestamp) {
                $ts = '<span class="notice-ts">' .
                    '<time datetime="' . dt::iso8601(time(), dcCore::app()->auth->getInfo('user_tz')) . '">' .
                    dt::str(__('%H:%M:%S'), null, dcCore::app()->auth->getInfo('user_tz')) .
                    '</time>' .
                    '</span> ';
            }
            $res = ($div ? '<div class="' . $class . '">' : '') . '<p' . ($div ? '' : ' class="' . $class . '"') . '>' .
                $ts . $msg .
                '</p>' . ($div ? '</div>' : '');
            if ($echo) {
                echo $res;
            }
        }

        return $res;
    }

    /**
     * Display a success message
     *
     * @param      string  $msg        The message
     * @param      bool    $timestamp  With the timestamp
     * @param      bool    $div        Inside a div (else in a p)
     * @param      bool    $echo       Display the message?
     *
     * @return     string
     */
    public static function success($msg, $timestamp = true, $div = false, $echo = true)
    {
        return self::message($msg, $timestamp, $div, $echo, 'success');
    }

    /**
     * Display a warning message
     *
     * @param      string  $msg        The message
     * @param      bool    $timestamp  With the timestamp
     * @param      bool    $div        Inside a div (else in a p)
     * @param      bool    $echo       Display the message?
     *
     * @return     string
     */
    public static function warning($msg, $timestamp = true, $div = false, $echo = true)
    {
        return self::message($msg, $timestamp, $div, $echo, 'warning-msg');
    }
}
