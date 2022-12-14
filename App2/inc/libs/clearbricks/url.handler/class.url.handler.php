<?php
/**
 * @class urlHandler
 *
 * @package Clearbricks
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class urlHandler
{
    protected $types = [];
    protected $default_handler;
    protected $error_handlers = [];
    public $mode;
    public $type = 'default';

    public function __construct($mode = 'path_info')
    {
        $this->mode = $mode;
    }

    public function register($type, $url, $representation, $handler)
    {
        $this->types[$type] = [
            'url'            => $url,
            'representation' => $representation,
            'handler'        => $handler,
        ];
    }

    public function registerDefault($handler)
    {
        $this->default_handler = $handler;
    }

    public function registerError($handler)
    {
        array_unshift($this->error_handlers, $handler);
    }

    public function unregister($type)
    {
        if (isset($this->types[$type])) {
            unset($this->types[$type]);
        }
    }

    public function getTypes()
    {
        return $this->types;
    }

    public function getBase($type)
    {
        if (isset($this->types[$type])) {
            return $this->types[$type]['url'];
        }
    }

    public function getDocument()
    {
        $type = $args = '';

        if ($this->mode == 'path_info') {
            $part = substr($_SERVER['PATH_INFO'], 1);
        } else {
            $part = '';

            $qs = $this->parseQueryString();

            # Recreates some _GET and _REQUEST pairs
            if (!empty($qs)) {
                foreach ($_GET as $k => $v) {
                    if (isset($_REQUEST[$k])) {
                        unset($_REQUEST[$k]);
                    }
                }
                $_GET     = $qs;
                $_REQUEST = array_merge($qs, $_REQUEST);

                foreach ($qs as $k => $v) {
                    if ($v === null) {
                        $part = $k;
                        unset($_GET[$k], $_REQUEST[$k]);
                    }

                    break;
                }
            }
        }

        $_SERVER['URL_REQUEST_PART'] = $part;

        $this->getArgs($part, $type, $args);

        if (!$type) {
            $this->type = 'default';
            $this->callDefaultHandler($args);
        } else {
            $this->type = $type;
            $this->callHandler($type, $args);
        }
    }

    public function getArgs($part, &$type, &$args)
    {
        if ($part == '') {
            $type = null;
            $args = null;

            return;
        }

        $this->sortTypes();

        foreach ($this->types as $k => $v) {
            $repr = $v['representation'];
            if ($repr == $part) {
                $type = $k;
                $args = null;

                return;
            } elseif (preg_match('#' . $repr . '#', (string) $part, $m)) {
                $type = $k;
                $args = $m[1] ?? null;

                return;
            }
        }

        # No type, pass args to default
        $args = $part;
    }

    public function callHandler($type, $args)
    {
        if (!isset($this->types[$type])) {
            throw new Exception('Unknown URL type');
        }

        $handler = $this->types[$type]['handler'];
        if (!is_callable($handler)) {
            throw new Exception('Unable to call function');
        }

        try {
            call_user_func($handler, $args);
        } catch (Exception $e) {
            foreach ($this->error_handlers as $err_handler) {
                if (call_user_func($err_handler, $args, $type, $e) === true) {
                    return;
                }
            }
            # propagate exception, as it has not been processed by handlers
            throw $e;
        }
    }

    public function callDefaultHandler($args)
    {
        if (!is_callable($this->default_handler)) {
            throw new Exception('Unable to call function');
        }

        try {
            call_user_func($this->default_handler, $args);
        } catch (Exception $e) {
            foreach ($this->error_handlers as $err_handler) {
                if (call_user_func($err_handler, $args, 'default', $e) === true) {
                    return;
                }
            }
            # propagate exception, as it has not been processed by handlers
            throw $e;
        }
    }

    protected function parseQueryString()
    {
        if (!empty($_SERVER['QUERY_STRING'])) {
            $q = explode('&', $_SERVER['QUERY_STRING']);
            $T = [];
            foreach ($q as $v) {
                $t = explode('=', $v, 2);

                $t[0] = rawurldecode($t[0]);
                if (!isset($t[1])) {
                    $T[$t[0]] = null;
                } else {
                    $T[$t[0]] = urldecode($t[1]);
                }
            }

            return $T;
        }

        return [];
    }

    protected function sortTypes()
    {
        $r = [];
        foreach ($this->types as $k => $v) {
            $r[$k] = $v['url'];
        }
        array_multisort($r, SORT_DESC, $this->types);
    }
}
