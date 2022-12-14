<?php
/**
 * @class http
 * @brief HTTP utilities
 *
 * @package Clearbricks
 * @subpackage Common
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class http
{
    public static $https_scheme_on_443 = false; ///< boolean: Force HTTPS scheme on server port 443 in {@link getHost()}
    public static $cache_max_age       = 0;     ///< integer: Cache max age for {@link cache()}
    public static $reverse_proxy       = false; ///< bolean: use X-FORWARD headers on getHost();

    /**
     * Self root URI
     *
     * Returns current scheme, host and port.
     *
     * @return string
     */
    public static function getHost(): string
    {
        if (self::$reverse_proxy && isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //admin have choose to allow a reverse proxy,
            //and HTTP_X_FORWARDED_FOR header means it's beeing using

            if (!isset($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
                throw new Exception('Reverse proxy parametter is setted, header HTTP_X_FORWARDED_FOR is found but not the X-Forwarded-Proto. Please check your reverse proxy server settings');
            }

            $scheme = $_SERVER['HTTP_X_FORWARDED_PROTO'];

            if (isset($_SERVER['HTTP_HOST'])) {
                $name_port_array = explode(':', $_SERVER['HTTP_HOST']);
            } else {
                // Fallback to server name and port
                $name_port_array = [
                    $_SERVER['SERVER_NAME'],
                    $_SERVER['SERVER_PORT'],
                ];
            }
            $server_name = $name_port_array[0];

            $port = isset($name_port_array[1]) ? ':' . $name_port_array[1] : '';
            if (($port == ':80' && $scheme == 'http') || ($port == ':443' && $scheme == 'https')) {
                $port = '';
            }

            return $scheme . '://' . $server_name . $port;
        }

        if (isset($_SERVER['HTTP_HOST'])) {
            $server_name = explode(':', $_SERVER['HTTP_HOST']);
            $server_name = $server_name[0];
        } else {
            // Fallback to server name
            $server_name = $_SERVER['SERVER_NAME'];
        }

        if (self::$https_scheme_on_443 && $_SERVER['SERVER_PORT'] == '443') {
            $scheme = 'https';
            $port   = '';
        } elseif (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $scheme = 'https';
            $port   = !in_array($_SERVER['SERVER_PORT'], ['80', '443']) ? ':' . $_SERVER['SERVER_PORT'] : '';
        } else {
            $scheme = 'http';
            $port   = ($_SERVER['SERVER_PORT'] != '80') ? ':' . $_SERVER['SERVER_PORT'] : '';
        }

        return $scheme . '://' . $server_name . $port;
    }

    /**
     * Self root URI
     *
     * Returns current scheme and host from a static URL.
     *
     * @param string    $url URL to retrieve the host from.
     *
     * @return string
     */
    public static function getHostFromURL(string $url): string
    {
        preg_match('~^(?:((?:[a-z]+:)?//)|:(//))?(?:([^:\r\n]*?)/[^:\r\n]*|([^:\r\n]*))$~', $url, $matches);
        array_shift($matches);

        return join($matches);
    }

    /**
     * Self URI
     *
     * Returns current URI with full hostname.
     *
     * @return string
     */
    public static function getSelfURI(): string
    {
        if (substr($_SERVER['REQUEST_URI'], 0, 1) != '/') {
            return self::getHost() . '/' . $_SERVER['REQUEST_URI'];
        }

        return self::getHost() . $_SERVER['REQUEST_URI'];
    }

    /**
     * Prepare a full redirect URI from a relative or absolute URL
     *
     * @param      string $page Relative URL
     * @return     string full URI
     */
    protected static function prepareRedirect(string $page): string
    {
        if (preg_match('%^http[s]?://%', $page)) {
            $redir = $page;
        } else {
            $host = self::getHost();

            if (substr($page, 0, 1) == '/') {
                $redir = $host . $page;
            } else {
                $dir = str_replace(DIRECTORY_SEPARATOR, '/', dirname($_SERVER['PHP_SELF']));
                if (substr($dir, -1) == '/') {
                    $dir = substr($dir, 0, -1);
                }
                if ($dir == '.') {
                    $dir = '';
                }
                $redir = $host . $dir . '/' . $page;
            }
        }

        return $redir;
    }

    /**
     * Redirect
     *
     * Performs a conforming HTTP redirect for a relative URL.
     *
     * @param string    $page        Relative URL
     */
    public static function redirect(string $page): string
    {
        # Close session if exists
        if (session_id()) {
            session_write_close();
        }

        header('Location: ' . self::prepareRedirect($page));
        exit;
    }

    /**
     * Concat URL and path
     *
     * Appends a path to a given URL. If path begins with "/" it will replace the
     * original URL path.
     *
     * @param string    $url        URL
     * @param string    $path    Path to append
     * @return string
     */
    public static function concatURL(string $url, string $path): string
    {
        if (substr($url, -1, 1) != '/') {
            $url .= '/';
        }

        if (substr($path, 0, 1) != '/') {
            return $url . $path;
        }

        return preg_replace('#^(.+?//.+?)/(.*)$#', '$1' . $path, $url);
    }

    /**
     * Real IP
     *
     * Returns the real client IP (or tries to do its best).
     *
     * @return string
     */
    public static function realIP(): ?string
    {
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }

    /**
     * Client unique ID
     *
     * Returns a "almost" safe client unique ID.
     *
     * @param string    $key        HMAC key
     * @return string
     */
    public static function browserUID(string $key): string
    {
        $uid = '';
        $uid .= $_SERVER['HTTP_USER_AGENT']     ?? '';
        $uid .= $_SERVER['HTTP_ACCEPT_CHARSET'] ?? '';

        return crypt::hmac($key, $uid);
    }

    /**
     * Client language
     *
     * Returns a two letters language code take from HTTP_ACCEPT_LANGUAGE.
     *
     * @return string
     */
    public static function getAcceptLanguage(): string
    {
        $dlang = '';
        if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $acclang = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $L       = explode(';', $acclang[0]);
            $dlang   = substr(trim((string) $L[0]), 0, 2);
        }

        return $dlang;
    }

    /**
     * Client languages
     *
     * Returns an array of accepted langages ordered by priority.
     * can be a two letters language code or a xx-xx variant.
     *
     * @return array
     */
    public static function getAcceptLanguages(): array
    {
        $langs = [];
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {

            // break up string into pieces (languages and q factors)
            preg_match_all(
                '/([a-z]{1,8}(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i',
                $_SERVER['HTTP_ACCEPT_LANGUAGE'],
                $lang_parse
            );

            if (count($lang_parse[1])) {
                // create a list like "en" => 0.8
                $langs = array_combine($lang_parse[1], $lang_parse[4]);

                // set default to 1 for any without q factor
                foreach ($langs as $lang => $val) {
                    if ($val === '') {
                        $langs[$lang] = 1;
                    }
                }

                // sort list based on value
                arsort($langs, SORT_NUMERIC);
                $langs = array_map('strtolower', array_keys($langs));
            }
        }

        return $langs;
    }

    /**
     * HTTP Cache
     *
     * Sends HTTP cache headers (304) according to a list of files and an optionnal.
     * list of timestamps.
     *
     * @param array        $files        Files on which check mtime
     * @param array        $mod_ts        List of timestamps
     */
    public static function cache(array $files, array $mod_ts = []): void
    {
        if (empty($files) || !is_array($files)) {
            return;
        }

        array_walk($files, function (&$v) {
            $v = filemtime($v);
        });

        $array_ts = array_merge($mod_ts, $files);

        rsort($array_ts);
        $now = time();
        $ts  = min($array_ts[0], $now);

        $since = null;
        if (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
            $since = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
            $since = preg_replace('/^(.*)(Mon|Tue|Wed|Thu|Fri|Sat|Sun)(.*)(GMT)(.*)/', '$2$3 GMT', $since);
            $since = strtotime($since);
            $since = ($since <= $now) ? $since : null;
        }

        # Common headers list
        $headers[] = 'Last-Modified: ' . gmdate('D, d M Y H:i:s', $ts) . ' GMT';
        $headers[] = 'Cache-Control: must-revalidate, max-age=' . abs((int) self::$cache_max_age);
        $headers[] = 'Pragma:';

        if ($since >= $ts) {
            self::head(304, 'Not Modified');
            foreach ($headers as $v) {
                header($v);
            }
            exit;
        }
        header('Date: ' . gmdate('D, d M Y H:i:s', $now) . ' GMT');
        foreach ($headers as $v) {
            header($v);
        }
    }

    /**
     * HTTP Etag
     *
     * Sends HTTP cache headers (304) according to a list of etags in client request.
     */
    public static function etag(): void
    {
        # We create an etag from all arguments
        $args = func_get_args();
        if (empty($args)) {
            return;
        }

        $etag = '"' . md5(implode('', $args)) . '"';
        unset($args);

        header('ETag: ' . $etag);

        # Do we have a previously sent content?
        if (!empty($_SERVER['HTTP_IF_NONE_MATCH'])) {
            foreach (explode(',', $_SERVER['HTTP_IF_NONE_MATCH']) as $i) {
                if (stripslashes(trim($i)) == $etag) {
                    self::head(304, 'Not Modified');
                    exit;
                }
            }
        }
    }

    /**
     * HTTP Header
     *
     * Sends an HTTP code and message to client.
     *
     * @param int    $code        HTTP code
     * @param string    $msg            Message
     */
    public static function head(int $code, $msg = null): void
    {
        $status_mode = preg_match('/cgi/', PHP_SAPI);

        if (!$msg) {
            $msg_codes = [
                100 => 'Continue',
                101 => 'Switching Protocols',
                200 => 'OK',
                201 => 'Created',
                202 => 'Accepted',
                203 => 'Non-Authoritative Information',
                204 => 'No Content',
                205 => 'Reset Content',
                206 => 'Partial Content',
                300 => 'Multiple Choices',
                301 => 'Moved Permanently',
                302 => 'Found',
                303 => 'See Other',
                304 => 'Not Modified',
                305 => 'Use Proxy',
                307 => 'Temporary Redirect',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                402 => 'Payment Required',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                406 => 'Not Acceptable',
                407 => 'Proxy Authentication Required',
                408 => 'Request Timeout',
                409 => 'Conflict',
                410 => 'Gone',
                411 => 'Length Required',
                412 => 'Precondition Failed',
                413 => 'Request Entity Too Large',
                414 => 'Request-URI Too Long',
                415 => 'Unsupported Media Type',
                416 => 'Requested Range Not Satisfiable',
                417 => 'Expectation Failed',
                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
                504 => 'Gateway Timeout',
                505 => 'HTTP Version Not Supported',
            ];

            $msg = $msg_codes[$code] ?? '-';
        }

        if ($status_mode) {
            header('Status: ' . $code . ' ' . $msg);
        } else {
            header($msg, true, $code);
        }
    }

    /**
     * Trim request
     *
     * Trims every value in GET, POST, REQUEST and COOKIE vars.
     * Removes magic quotes if magic_quote_gpc is on.
     */
    public static function trimRequest(): void
    {
        if (!empty($_GET)) {
            array_walk($_GET, ['self', 'trimRequestInVar']);    // @phpstan-ignore-line
        }
        if (!empty($_POST)) {
            array_walk($_POST, ['self', 'trimRequestInVar']);   // @phpstan-ignore-line
        }
        if (!empty($_REQUEST)) {
            array_walk($_REQUEST, ['self', 'trimRequestInVar']);    // @phpstan-ignore-line
        }
        if (!empty($_COOKIE)) {
            array_walk($_COOKIE, ['self', 'trimRequestInVar']); // @phpstan-ignore-line
        }
    }

    private static function trimRequestInVar(&$value, $key): void
    {
        if (is_array($value)) {
            foreach ($value as $k => &$v) {
                if (is_array($v)) {
                    self::trimRequestInVar($v, $k);
                } else {
                    $v = trim((string) $v);
                }
            }
        } else {
            $value = trim((string) $value);
        }
    }

    /**
     * Unset global variables (obsolete)
     */
    public static function unsetGlobals(): void
    {
    }
}
