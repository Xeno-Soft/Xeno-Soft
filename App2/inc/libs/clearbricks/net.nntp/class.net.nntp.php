<?php
/**
 * @class netNntp
 *
 * @package Clearbricks
 * @subpackage Network
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class netNntp extends netSocket
{
    public const SERVER_READY         = 200;
    public const SERVER_READY_NO_POST = 201;
    public const GROUP_SELECTED       = 211;
    public const INFORMATION_FOLLOWS  = 215;
    public const ARTICLE_HEAD_BODY    = 220;
    public const ARTICLE_HEAD         = 221;
    public const ARTICLE_BODY         = 222;
    public const ARTICLE_OVERVIEW     = 224;
    public const NEW_ARTICLES         = 230;
    public const ARTICLE_POST_OK      = 240;
    public const ARTICLE_POST_READY   = 340;
    public const AUTH_ACCEPT          = 281;
    public const MORE_AUTH_INFO       = 381;
    public const AUTH_REQUIRED        = 480;
    public const AUTH_REJECTED        = 482;
    public const NOT_IMPLEMENTED      = 500;
    public const NO_PERMISSION        = 502;

    protected $host;
    protected $port;
    protected $user;
    protected $password;

    protected $proxy_host;
    protected $proxy_port;
    protected $proxy_user;
    protected $proxy_pass;
    protected $use_proxy;

    /**
     * Constructs a new instance.
     *
     * @param      mixed   $host      The host
     * @param      int     $port      The port
     * @param      mixed   $user      The user
     * @param      mixed   $password  The password
     * @param      int     $timeout   The timeout
     */
    public function __construct($host, $port = 119, $user = null, $password = null, $timeout = 10)
    {
        $this->host     = $host;
        $this->port     = (int) $port;
        $this->user     = $user;
        $this->password = $password;
        $this->_timeout = $timeout;
    }

    /**
     * Send data
     *
     * @param      mixed  $data   The data
     *
     * @return     mixed
     */
    public function write($data)
    {
        if (!is_array($data)) {
            $data = $data . "\r\n";
        }

        return parent::write($data);
    }

    public function close()
    {
        if ($this->isOpen()) {
            $this->sendRequest('quit');
            parent::close();
        }
    }

    /**
     * Open a connection
     *
     * @throws     Exception
     *
     * @return     mixed
     */
    public function open()
    {
        if ($this->isOpen()) {
            return true;
        }

        if ($this->use_proxy) {
            $this->_host = $this->proxy_host;
            $this->_port = $this->proxy_port;
        } else {
            $this->_host = $this->host;
            $this->_port = $this->port;
        }

        $rsp = parent::open();

        if ($this->isOpen()) {
            if ($this->use_proxy) {
                $data[] = 'CONNECT ' . $this->host . ':' . $this->port . ' HTTP/1.0';
                if ($this->proxy_user && $this->proxy_pass) {
                    $data[] = 'Proxy-Authorization: Basic ' .
                    base64_encode($this->proxy_user . ':' . $this->proxy_pass);
                }

                foreach ($this->write($data) as $i => $v) {
                    if ($i == 0) {
                        if (stristr($v, '200 Connection established')) {
                            continue;
                        }
                        $rsp = [
                            'status'  => self::NO_PERMISSION, # Assign it to something dummy
                            'message' => 'No permission',
                        ];

                        break;
                    }
                    if ($i == 2) {
                        $rsp = $this->parseResponse($v);

                        break;
                    }
                }
            } else {
                $rsp = $this->parseResponse($rsp->current());
            }

            if (($rsp['status'] == self::SERVER_READY) || ($rsp['status'] == self::SERVER_READY_NO_POST)) {
                $this->sendRequest('mode reader');
                if ($this->user) {
                    $rsp = $this->parseResponse($this->sendRequest('authinfo user ' . $this->user));

                    if ($rsp['status'] == self::MORE_AUTH_INFO) {
                        $rsp = $this->parseResponse($this->sendRequest('authinfo pass ' . $this->password));

                        if ($rsp['status'] == self::AUTH_ACCEPT) {
                            return true;
                        }
                    }
                } else {
                    return true;
                }
            }

            throw new Exception($rsp['status'] . ' - ' . $rsp['message']);
        }

        return false;
    }

    public function setUser($user, $password = null)
    {
        $this->user     = $user;
        $this->password = $password;

        if ($this->isOpen()) {
            $this->close();
            $this->open();
        }
    }

    public function setProxy($proxy_host, $proxy_port = null, $proxy_user = null, $proxy_pass = null)
    {
        $this->proxy_host = $proxy_host;
        $this->proxy_port = $proxy_port;
        $this->proxy_user = $proxy_user;
        $this->proxy_pass = $proxy_pass;

        if ((strcmp($this->proxy_host, '') != 0) && (strcmp($this->proxy_port, '') != 0)) {
            $this->use_proxy = true;
        } else {
            $this->use_proxy = false;
        }
    }

    public function getGroupsList($group_pattern = null)
    {
        $rsp = $this->write('list active ' . $group_pattern);
        $r   = $this->parseResponse($rsp->current());

        if ($r['status'] == self::INFORMATION_FOLLOWS) {
            # List groups
            $result = [];
            foreach ($rsp as $buf) {
                if (preg_match('/^\.\s*$/', $buf)) {
                    break;
                }

                [$group, $last, $first, $post] = preg_split('/\s+/', $buf, 4);
                $result[$group]                = [
                    'desc'  => '',
                    'last'  => trim((string) $last),
                    'first' => trim((string) $first),
                    'post'  => strtolower(trim((string) $post)),
                ];
            }

            # Get groups descriptions
            $rsp = $this->write(['list newsgroups ' . $group_pattern]);
            $r   = $this->parseResponse($rsp->current());

            if ($r['status'] == self::INFORMATION_FOLLOWS) {
                foreach ($rsp as $buf) {
                    if (self::eot($buf)) {
                        break;
                    }

                    [$group, $desc] = preg_split('/\s+/', $buf, 2);
                    if (isset($result[$group])) {
                        $result[$group]['desc'] = text::toUTF8(trim((string) $desc));
                    }
                }
            }

            return $result;
        }

        throw new Exception($r['message'] . ' - (' . $r['status'] . ')');
    }

    public function joinGroup($group)
    {
        $rsp = $this->parseResponse($this->sendRequest('group ' . $group));

        if ($rsp['status'] == self::GROUP_SELECTED) {
            $result = preg_split("/\s/", $rsp['message']);

            return [
                'count'    => $result[0],
                'start_id' => $result[1],
                'end_id'   => $result[2],
                'group'    => $result[3],
            ];
        }

        throw new Exception($rsp['message'] . ' - (' . $rsp['status'] . ')');
    }

    public function getArticleList($group = null)
    {
        $rsp = $this->write('listgroup ' . $group);
        $r   = $this->parseResponse($rsp->current());

        if ($r['status'] == self::GROUP_SELECTED) {
            $res = [];
            foreach ($rsp as $i => $buf) {
                if (self::eot($buf)) {
                    break;
                }
                $res[] = trim((string) $buf);
            }

            return $res;
        }

        throw new Exception($r['message'] . ' - (' . $r['status'] . ')');
    }

    public function getNewArticles($ts, $group)
    {
        $ts = $ts + dt::getTimeOffset('UTC', $ts);

        # First try with newnews
        $rsp = $this->write('newnews ' . $group . ' ' . dt::str('%y%m%d %H%M%S', $ts) . ' GMT');
        $r   = $this->parseResponse($rsp->current());

        if ($r['status'] == self::NEW_ARTICLES) {
            $res = [];
            $rsp->current(); # we don't want first matched article
            foreach ($rsp as $buf) {
                if (self::eot($buf)) {
                    break;
                }
                $res[] = trim((string) $buf);
            }

            return $res;
        }
        # newnews is not implemented, use xhdr instead
        # First, we need to join the group
        $g = $this->joinGroup($group);

        if ($g['count'] > 1000) {
            $g['start_id'] = $g['end_id'] - 1000;
        }

        # Then, xhdr on all group messages
        $rsp = $this->write('xhdr date ' . $g['start_id'] . '-');
        $r   = $this->parseResponse($rsp->current());

        if ($r['status'] == self::ARTICLE_HEAD) {
            $ts = $ts + dt::getTimeOffset('UTC', $ts);

            $res = [];
            foreach ($rsp as $buf) {
                if (self::eot($buf)) {
                    break;
                }
                $buf = preg_split('/\s/', $buf, 2);
                if (strtotime($buf[1]) > $ts) {
                    $res[] = $buf[0];
                }
            }

            return $res;
        }

        throw new Exception($r['message'] . ' - (' . $r['status'] . ')');
    }

    public function getHeader($message_id, &$header = '')
    {
        $rsp = $this->write('head ' . $message_id);
        $r   = $this->parseResponse($rsp->current());

        if ($r['status'] == self::ARTICLE_HEAD || $r['status'] == self::ARTICLE_HEAD_BODY) {
            $header = '';
            foreach ($rsp as $buf) {
                if (self::eot($buf)) {
                    break;
                }
                $header .= $buf;
            }

            return new nntpMessage($header);
        }

        throw new Exception($r['message'] . ' - (' . $r['status'] . ')');
    }

    public function getArticle($message_id, &$article = '')
    {
        $rsp = $this->write('article ' . $message_id);
        $r   = $this->parseResponse($rsp->current());

        if ($r['status'] == self::ARTICLE_BODY || $r['status'] == self::ARTICLE_HEAD_BODY) {
            $article = '';
            foreach ($rsp as $buf) {
                if (self::eot($buf)) {
                    break;
                }
                $article .= $buf;
            }

            return new nntpMessage($article);
        }

        throw new Exception($r['message'] . ' - (' . $r['status'] . ')');
    }

    public function postArticle($headers = [], $content = '')
    {
        if (!is_array($headers)) {
            return false;
        }

        if (empty($headers['From'])) {
            throw new Exception('No "From" header in message');
        }

        $headers['Mime-Version']              = '1.0';
        $headers['Content-Type']              = 'text/plain; charset=UTF-8';
        $headers['Content-Transfer-Encoding'] = 'quoted-printable';

        $content = mailConvert::rewrap($content);
        $content = preg_replace('/^\./msu', '..$1', $content);
        $content = text::QPEncode($content);

        $data = [];
        # Headers
        foreach ($headers as $k => $v) {
            $data[] = $k . ': ' . $v;
        }

        # Blank line
        $data[] = '';

        # Body
        foreach (preg_split("/\r?\n/msu", $content) as $l) {
            $data[] = $l;
        }

        # EOT
        $data[] = '.';

        $this->sendRequest('post');

        $rsp = $this->write($data);
        $r   = $this->parseResponse($rsp->current());
        if ($r['status'] == self::ARTICLE_POST_OK) {
            return true;
        }

        throw new Exception($r['message'] . ' - (' . $r['status'] . ')');
    }

    protected static function eot($l)
    {
        return preg_match('/^\.\s*$/', $l);
    }

    protected function assertOpen()
    {
        if (!$this->isOpen()) {
            throw new Exception('NNTP connexion not available');
        }
    }

    protected function parseResponse($rsp)
    {
        return [
            'status'  => substr($rsp, 0, 3),
            'message' => rtrim(substr($rsp, 4), "\r\n"),
        ];
    }

    protected function sendRequest($request)
    {
        $this->assertOpen();

        $rsp = $this->write($request);
        $this->flush();

        return $rsp->current();
    }
}
