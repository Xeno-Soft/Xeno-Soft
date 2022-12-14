<?php
/**
 * @class l10n
 * @brief Localization tools
 *
 * Localization utilities
 *
 * @package Clearbricks
 * @subpackage Common
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */

/* @cond ONCE */
if (!function_exists('__')) {
    /** @endcond */

    /**
     * Translated string
     *
     * @see l10n::trans()
     *
     * @param      string   $singular Singular form of the string
     * @param      string   $plural Plural form of the string (optionnal)
     * @param      integer  $count Context number for plural form (optionnal)
     * @return     string   translated string
     */
    function __(string $singular, ?string $plural = null, ?int $count = null): string
    {
        return l10n::trans($singular, $plural, $count);
    }

    /* @cond ONCE */
}
/** @endcond */
class l10n
{
    /// @name Languages properties
    //@{
    protected static $languages_definitions      = [];
    protected static $languages_name             = null;
    protected static $languages_textdirection    = null;
    protected static $languages_pluralsnumber    = null;
    protected static $languages_pluralexpression = null;
    //@}

    /// @name Current language properties
    //@{
    protected static $language_code             = null;
    protected static $language_name             = null;
    protected static $language_textdirection    = null;
    protected static $language_pluralsnumber    = null;
    protected static $language_pluralexpression = null;
    protected static $language_pluralfunction   = null;
    //@}

    /** @deprecated */
    public static $text_direction;

    /** @deprecated */
    protected static $langs = [];

    /**
     * L10N initialization
     *
     * Create global arrays for L10N stuff. Should be called before any work
     * with other methods. For plural-forms, __l10n values can now be array.
     *
     * @param string $code Language code to work with
     */
    public static function init($code = 'en')
    {
        $GLOBALS['__l10n'] = $GLOBALS['__l10n_files'] = [];

        self::lang($code);
    }

    /**
     * Set a language to work on or return current working language code
     *
     * This set up language properties to manage plurals form.
     * Change of language code not reset global array of L10N stuff.
     *
     * @param string $code Language code
     * @return string Current language code
     */
    public static function lang(?string $code = null): string
    {
        if ($code !== null && self::$language_code != $code && self::isCode($code)) {
            self::$language_code             = $code;
            self::$language_name             = self::getLanguageName($code);
            self::$language_textdirection    = self::getLanguageTextDirection($code);
            self::$language_pluralsnumber    = self::getLanguagePluralsNumber($code);
            self::$language_pluralexpression = self::getLanguagePluralExpression($code);

            self::$language_pluralfunction = self::createPluralFunction(
                self::$language_pluralsnumber,
                self::$language_pluralexpression
            );

            // Backwards compatibility
            self::$text_direction = self::$language_textdirection;
        }

        return self::$language_code;
    }

    /**
     * Translate a string
     *
     * Returns a translated string of $singular
     * or $plural according to a number if it is set.
     * If translation is not found, returns the string.
     *
     * @param string $singular Singular form of the string
     * @param string $plural Plural form of the string (optionnal)
     * @param integer $count Context number for plural form (optionnal)
     * @return string Translated string
     */
    public static function trans(string $singular, ?string $plural = null, ?int $count = null): string
    {
        // If no string to translate, return no string
        if ($singular == '') {
            return '';

        // If no l10n translation loaded or exists
        } elseif ((!array_key_exists('__l10n', $GLOBALS) || empty($GLOBALS['__l10n'])
            || !array_key_exists($singular, $GLOBALS['__l10n'])) && is_null($count)) {
            return $singular;

        // If no $plural form or if current language has no plural form return $singular translation
        } elseif ($plural === null || $count === null || self::$language_pluralsnumber == 1) {
            $t = !empty($GLOBALS['__l10n'][$singular]) ? $GLOBALS['__l10n'][$singular] : $singular;

            return is_array($t) ? $t[0] : $t;

            // Else return translation according to $count
        }
        $i = self::index($count);

        // If it is a plural and translation exists in "singular" form
        if ($i > 0 && !empty($GLOBALS['__l10n'][$plural])) {
            $t = $GLOBALS['__l10n'][$plural];

            return is_array($t) ? $t[0] : $t;

        // If it is plural and index exists in plurals translations
        } elseif (!empty($GLOBALS['__l10n'][$singular])
                && is_array($GLOBALS['__l10n'][$singular])
                && array_key_exists($i, $GLOBALS['__l10n'][$singular])
                && !empty($GLOBALS['__l10n'][$singular][$i])) {
            return $GLOBALS['__l10n'][$singular][$i];

            // Else return input string according to "en" plural form
        }

        return $i > 0 ? $plural : $singular;
    }

    /**
     * Retrieve plural index from input number
     *
     * @param integer $count Number to take account
     * @return integer Index of plural form
     */
    public static function index(int $count): int
    {
        return call_user_func(self::$language_pluralfunction, $count);
    }

    /**
     * Add a file
     *
     * Adds a l10n file in translation strings. $file should be given without
     * extension. This method will look for $file.lang.php and $file.po (in this
     * order) and retrieve the first one found.
     * We not care about language (and plurals forms) of the file.
     *
     * @param string    $file        Filename (without extension)
     * @return boolean True on success
     */
    public static function set(string $file): bool
    {
        $lang_file = $file . '.lang';
        $po_file   = $file . '.po';
        $php_file  = $file . '.lang.php';

        if (file_exists($php_file)) {
            require $php_file;
        } elseif (($tmp = self::getPoFile($po_file)) !== false) {
            $GLOBALS['__l10n_files'][] = $po_file;
            $GLOBALS['__l10n']         = $tmp + $GLOBALS['__l10n']; // "+" erase numeric keys unlike array_merge
        } elseif (($tmp = self::getLangFile($lang_file)) !== false) {
            $GLOBALS['__l10n_files'][] = $lang_file;
            $GLOBALS['__l10n']         = $tmp + $GLOBALS['__l10n']; // "+" erase numeric keys unlike array_merge
        } else {
            return false;
        }

        return true;
    }

    /**
     * L10N file
     *
     * Returns a file path for a file, a directory and a language.
     * If $dir/$lang/$file is not found, it will check if $dir/en/$file
     * exists and returns the result. Returns false if no file were found.
     *
     * @param string    $dir        Directory
     * @param string    $file    File
     * @param string    $lang    Language
     * @return string|false        File path or false
     */
    public static function getFilePath(string $dir, string $file, string $lang)
    {
        $f = $dir . '/' . $lang . '/' . $file;
        if (!file_exists($f)) {
            $f = $dir . '/en/' . $file;
        }

        return file_exists($f) ? $f : false;
    }

    /** @deprecated */
    public static function getLangFile(string $file)
    {
        if (!file_exists($file)) {
            return false;
        }

        $fp = @fopen($file, 'r');
        if ($fp === false) {
            return false;
        }

        $res = [];
        while ($l = fgets($fp)) {
            $l = trim($l);
            # Comment
            if (substr($l, 0, 1) == '#') {
                continue;
            }

            # Original text
            if (substr($l, 0, 1) == ';' && ($t = fgets($fp)) !== false && trim($t) != '') {
                $res[substr($l, 1)] = trim($t);
            }
        }
        fclose($fp);

        return $res;
    }

    /// @name Gettext PO methods
    //@{
    /**
     * Load gettext file
     *
     * Returns an array of strings found in a given gettext (.po) file
     *
     * @param string    $file        Filename
     * @return array|false
     */
    public static function getPoFile(string $file)
    {
        if (($m = self::parsePoFile($file)) === false) {
            return false;
        }

        if (empty($m[1])) {
            return [];
        }

        // Keep singular id and translations, remove headers and comments
        $r = [];
        foreach ($m[1] as $v) {
            if (isset($v['msgid']) && isset($v['msgstr'])) {
                $r[$v['msgid']] = $v['msgstr'];
            }
        }

        return $r;
    }

    /**
     * Generates a PHP file from a po file
     *
     * Return a boolean depending on success or failure
     *
     * @param      string $file File
     * @param      string $license_block optional license block to add at the beginning
     * @return     boolean true on success
     */
    public static function generatePhpFileFromPo(string $file, string $license_block = ''): bool
    {
        $po_file  = $file . '.po';
        $php_file = $file . '.lang.php';

        $strings  = self::getPoFile($po_file);
        $fcontent = "<?php\n" .
            $license_block .
            "#\n#\n#\n" .
            "#        DOT NOT MODIFY THIS FILE !\n\n\n\n\n";

        foreach ($strings as $vo => $tr) {
            $vo = str_replace("'", "\\'", $vo);
            if (is_array($tr)) {
                foreach ($tr as $i => $t) {
                    $t = str_replace("'", "\\'", $t);
                    $fcontent .= '$GLOBALS[\'__l10n\'][\'' . $vo . '\'][' . $i . '] = \'' . $t . '\';' . "\n";
                }
            } else {
                $tr = str_replace("'", "\\'", $tr);
                $fcontent .= '$GLOBALS[\'__l10n\'][\'' . $vo . '\'] = \'' . $tr . '\';' . "\n";
            }
        }

        if (($fp = fopen($php_file, 'w')) !== false) {
            fwrite($fp, $fcontent, strlen($fcontent));
            fclose($fp);

            return true;
        }

        return false;
    }

    /**
     * Parse Po File
     *
     * Return an array of po headers and translations from a po file
     *
     * @param string $file File path
     * @return array|false Parsed file
     */
    public static function parsePoFile(string $file)
    {
        // stop if file not exists
        if (!file_exists($file)) {
            return false;
        }

        // read file per line in array (without ending new line)
        if (false === ($lines = file($file, FILE_IGNORE_NEW_LINES))) {
            return false;
        }

        // prepare variables
        $headers = [
            'Project-Id-Version'        => '',
            'Report-Msgid-Bugs-To'      => '',
            'POT-Creation-Date'         => '',
            'PO-Revision-Date'          => '',
            'Last-Translator'           => '',
            'Language-Team'             => '',
            'Content-Type'              => '',
            'Content-Transfer-Encoding' => '',
            'Plural-Forms'              => '',
            // there are more headers but these ones are default
        ];
        $headers_searched = $headers_found = false;
        $h_line           = $h_val           = $h_key           = '';
        $entries          = $entry          = $desc          = [];
        $i                = 0;

        // read through lines
        for ($i = 0; $i < count($lines); $i++) {

            // some people like mirovinben add white space at the end of line
            $line = trim((string) $lines[$i]);

            // jump to next line on blank one or empty comment (#)
            if (strlen($line) < 2) {
                continue;
            }

            // headers
            if (!$headers_searched && preg_match('/^msgid\s+""$/', trim((string) $line))) {

                // headers start wih empty msgid and msgstr follow be multine
                if (!preg_match('/^msgstr\s+""$/', trim((string) $lines[$i + 1]))
                    || !preg_match('/^"(.*)"$/', trim((string) $lines[$i + 2]))) {
                    $headers_searched = true;
                } else {
                    $l = $i + 2;
                    while (false !== ($def = self::cleanPoLine('multi', $lines[$l++]))) {
                        $h_line = self::cleanPoString($def[1]);

                        // an header has key:val
                        if (false === ($h_index = strpos($h_line, ':'))) {

                            // multiline value
                            if (!empty($h_key) && !empty($headers[$h_key])) {
                                $headers[$h_key] = trim((string) $headers[$h_key] . $h_line);

                                continue;

                                // your .po file is so bad
                            }
                            $headers_searched = true;

                            break;
                        }

                        // extract key and value
                        $h_key = substr($h_line, 0, $h_index);
                        $h_val = substr($h_line, $h_index + 1);

                        // unknow header
                        if (!isset($headers[$h_key])) {
                            //continue;
                        }

                        // ok it's an header, add it
                        $headers[$h_key] = trim($h_val);
                        $headers_found   = true;
                    }

                    // headers found so stop search and clean previous comments
                    if ($headers_found) {
                        $headers_searched = true;
                        $entry            = $desc            = [];
                        $i                = $l - 1;

                        continue;
                    }
                }
            }

            // comments
            if (false !== ($def = self::cleanPoLine('comment', $line))) {
                $str = self::cleanPoString($def[2]);

                switch ($def[1]) {

                    // translator comments
                    case ' ':
                        if (!isset($desc['translator-comments'])) {
                            $desc['translator-comments'] = $str;
                        } else {
                            $desc['translator-comments'] .= "\n" . $str;
                        }

                        break;

                    // extracted comments
                    case '.':
                        if (!isset($desc['extracted-comments'])) {
                            $desc['extracted-comments'] = $str;
                        } else {
                            $desc['extracted-comments'] .= "\n" . $str;
                        }

                        break;

                    // reference
                    case ':':
                        if (!isset($desc['references'])) {
                            $desc['references'] = [];
                        }
                        $desc['references'][] = $str;

                        break;

                    // flag
                    case ',':
                        if (!isset($desc['flags'])) {
                            $desc['flags'] = [];
                        }
                        $desc['flags'][] = $str;

                        break;

                    // previous msgid, msgctxt
                    case '|':
                        // msgid
                        if (strpos($def[2], 'msgid') === 0) {
                            $desc['previous-msgid'] = $str;
                        // msgcxt
                        } else {
                            $desc['previous-msgctxt'] = $str;
                        }

                        break;
                }
            }

            // msgid
            elseif (false !== ($def = self::cleanPoLine('msgid', $line))) {

                // add last translation and start new one
                if ((isset($entry['msgid']) || isset($entry['msgid_plural'])) && isset($entry['msgstr'])) {

                    // save last translation and start new one
                    $entries[] = $entry;
                    $entry     = [];

                    // add comments to new translation
                    if (!empty($desc)) {
                        $entry = array_merge($entry, $desc);
                        $desc  = [];
                    }

                    // stop searching headers
                    $headers_searched = true;
                }

                $str = self::cleanPoString($def[2]);

                // msgid_plural
                if (!empty($def[1])) {
                    $entry['msgid_plural'] = $str;
                } else {
                    $entry['msgid'] = $str;
                }
            }

            // msgstr
            elseif (false !== ($def = self::cleanPoLine('msgstr', $line))) {
                $str = self::cleanPoString($def[2]);

                // plural forms
                if (!empty($def[1])) {
                    if (!isset($entry['msgstr'])) {
                        $entry['msgstr'] = [];
                    }
                    $entry['msgstr'][] = $str;
                } else {
                    $entry['msgstr'] = $str;
                }
            }

            // multiline
            elseif (false !== ($def = self::cleanPoLine('multi', $line))) {
                $str = self::cleanPoString($def[1]);

                // msgid
                if (!isset($entry['msgstr'])) {
                    //msgid plural
                    if (isset($entry['msgid_plural'])) {
                        if (!is_array($entry['msgid_plural'])) {
                            $entry['msgid_plural'] .= $str;
                        } else {
                            $entry['msgid_plural'][count($entry['msgid_plural']) - 1] .= $str;
                        }
                    } else {
                        if (!is_array($entry['msgid'])) {
                            $entry['msgid'] .= $str;
                        } else {
                            $entry['msgid'][count($entry['msgid']) - 1] .= $str;
                        }
                    }

                    // msgstr
                } else {
                    if (!is_array($entry['msgstr'])) {
                        $entry['msgstr'] .= $str;
                    } else {
                        $entry['msgstr'][count($entry['msgstr']) - 1] .= $str;
                    }
                }
            }
        }

        // Add last translation
        if (!empty($entry)) {
            if (!empty($desc)) {
                $entry = array_merge($entry, $desc);
            }
            $entries[] = $entry;
        }

        return [$headers, $entries];
    }

    /* @ignore */
    protected static function cleanPoLine($type, $_)
    {
        $patterns = [
            'msgid'   => 'msgid(_plural|)\s+"(.*)"',
            'msgstr'  => 'msgstr(\[.*?\]|)\s+"(.*)"',
            'multi'   => '"(.*)"',
            'comment' => '#\s*(\s|\.|:|\,|\|)\s*(.*)',
        ];

        if (array_key_exists($type, $patterns)
            && preg_match('/^' . $patterns[$type] . '$/i', trim((string) $_), $m)) {
            return $m;
        }

        return false;
    }

    /* @ignore */
    protected static function cleanPoString($_): string
    {
        return stripslashes(str_replace(['\n', '\r\n'], "\n", $_));
    }

    /**
     * Extract nplurals and plural from po expression
     *
     * @param string $expression Plural form as of gettext Plural-form param
     * @return array Number of plurals and cleaned plural expression
     */
    public static function parsePluralExpression(string $expression): array
    {
        return preg_match('/^\s*nplurals\s*=\s*(\d+)\s*;\s+plural\s*=\s*(.+)$/', $expression, $m) ?
        [(int) $m[1], trim(self::cleanPluralExpression($m[2]))] :
        [self::$language_pluralsnumber, self::$language_pluralexpression];
    }

    /**
     * Create function to find plural msgstr index from gettext expression
     *
     * @param integer $nplurals Plurals number
     * @param string $expression Plural expression
     * @return callable Function to extract right plural index
     */
    public static function createPluralFunction(int $nplurals, string $expression)
    {
        return function ($n) use ($nplurals, $expression) {
            $i = eval('return (integer) (' . str_replace('n', (string) $n, $expression) . ');');

            return ($i < $nplurals) ? $i : $nplurals - 1;
        };
    }

    /* @ignore */
    protected static function cleanPluralExpression(string $_): string
    {
        $_ .= ';';
        $r = '';
        $l = 0;

        for ($i = 0; $i < strlen($_); ++$i) {
            switch ($_[$i]) {
                case '?':
                    $r .= ' ? (';
                    $l++;

                    break;

                case ':':
                    $r .= ') : (';

                    break;

                case ';':
                    $r .= str_repeat(')', $l) . ';';
                    $l = 0;

                    break;

                default:
                    $r .= $_[$i];
            }
        }

        return rtrim($r, ';');
    }
    //@}

    /// @name Languages definitions methods
    //@{
    /**
     * Check if a language code exists
     *
     * @param string $code Language code
     * @return boolean True if code exists
     */
    public static function isCode(string $code): bool
    {
        return array_key_exists($code, self::getLanguagesName());
    }

    /**
     * Get a language code according to a language name
     *
     * @param string $code Language name
     * @return string Language code
     */
    public static function getCode(string $code): string
    {
        $_ = self::getLanguagesName();

        return (($index = array_search($code, $_)) !== false) ? $index : self::$language_code;
    }

    /**
     * ISO Codes
     *
     * @param boolean    $flip            Flip resulting array
     * @param boolean    $name_with_code    Prefix (code) to names
     * @return array
     */
    public static function getISOcodes(bool $flip = false, bool $name_with_code = false): array
    {
        $langs = self::getLanguagesName();
        if ($name_with_code) {
            foreach ($langs as $k => &$v) {
                $v = $k . ' - ' . $v;
            }
        }

        if ($flip) {
            return array_flip($langs);
        }

        return $langs;
    }

    /**
     * Get a language name according to a lang code
     *
     * @param string $code Language code
     * @return string Language name
     */
    public static function getLanguageName(string $code): string
    {
        $_ = self::getLanguagesName();

        return array_key_exists($code, $_) ? $_[$code] : self::$language_name;
    }

    /**
     * Get languages names
     *
     * @return array List of languages names by languages codes
     */
    public static function getLanguagesName(): array
    {
        if (empty(self::$languages_name)) {
            self::$languages_name = self::getLanguagesDefinitions(3);

            // Backwards compatibility
            self::$langs = self::$languages_name;
        }

        return self::$languages_name;
    }

    /**
     * Get a text direction according to a language code
     *
     * @param string $code Language code
     * @return string Text direction (rtl or ltr)
     */
    public static function getLanguageTextDirection(string $code): string
    {
        $_ = self::getLanguagesTextDirection();

        return array_key_exists($code, $_) ? $_[$code] : self::$language_textdirection;
    }

    /**
     * Get languages text directions
     *
     * @return array List of text directions by languages codes
     */
    public static function getLanguagesTextDirection(): array
    {
        if (empty(self::$languages_textdirection)) {
            self::$languages_textdirection = self::getLanguagesDefinitions(4);
        }

        return self::$languages_textdirection;
    }

    /**
     * Text direction
     *
     * @deprecated
     * @see l10n::getLanguageTextDirection()
     *
     * @param string    $lang    Language code
     * @return string ltr or rtl
     */
    public static function getTextDirection(string $lang): string
    {
        return self::getLanguageTextDirection($lang);
    }

    /**
     * Get a number of plurals according to a language code
     *
     * @param string $code Language code
     * @return integer Number of plurals
     */
    public static function getLanguagePluralsNumber(string $code): int
    {
        $_ = self::getLanguagesPluralsNumber();

        return !empty($_[$code]) ? $_[$code] : self::$language_pluralsnumber;
    }

    /**
     * Get languages numbers of plurals
     *
     * @return array List of numbers of plurals by languages codes
     */
    public static function getLanguagesPluralsNumber(): array
    {
        if (empty(self::$languages_pluralsnumber)) {
            self::$languages_pluralsnumber = self::getLanguagesDefinitions(5);
        }

        return self::$languages_pluralsnumber;
    }

    /**
     * Get a plural expression according to a language code
     *
     * @param string $code Language code
     * @return string Plural expression
     */
    public static function getLanguagePluralExpression(string $code): string
    {
        $_ = self::getLanguagesPluralExpression();

        return !empty($_[$code]) ? $_[$code] : self::$language_pluralexpression;
    }

    /**
     * Get languages plural expressions
     *
     * @return array List of plural expressions by languages codes
     */
    public static function getLanguagesPluralExpression(): array
    {
        if (empty(self::$languages_pluralexpression)) {
            self::$languages_pluralexpression = self::getLanguagesDefinitions(6);
        }

        return self::$languages_pluralexpression;
    }

    /**
     * Get languages definitions of a given type
     *
     * The list follows ISO 639.1 norm with additionnal IETF codes as pt-br
     *
     * Countries codes and names from:
     * - http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
     * - http://www.gnu.org/software/gettext/manual/gettext.html#Language-Codes
     * - http://www.loc.gov/standards/iso639-2/php/English_list.php
     *
     * Text direction from:
     * - http://translate.sourceforge.net/wiki/l10n/displaysettings
     * - http://meta.wikimedia.org/wiki/Template:List_of_language_names_ordered_by_code
     *
     * Plural-forms taken from:
     * - http://translate.sourceforge.net/wiki/l10n/pluralforms
     *
     * $languages_definitions types look like this:
     * 0 = code ISO 639.1 (2 digit) + IETF add
     * 1 = code ISO 639.2 (english 3 digit)
     * 2 = English name
     * 3 = natal name
     * 4 = text direction (ltr or rtl)
     * 5 = number of plurals (1 means no plural form)
     * 6 = plural expression (as of gettext .po plural form)
     *
     * null values represent missing values
     *
     * @param integer $type Type of definition
     * @param string $default Default value if definition is empty
     * @return array List of requested definition by languages codes
     */
    protected static function getLanguagesDefinitions(int $type, string $default = ''): array
    {
        if ($type < 0 || $type > 6) {
            return [];
        }

        if (empty(self::$languages_definitions)) {
            self::$languages_definitions = [
                ['aa', 'aar', 'Afar', 'Afaraf', 'ltr', null, null],
                ['ab', 'abk', 'Abkhazian', '??????????', 'ltr', null, null],
                ['ae', 'ave', 'Avestan', 'Avesta', 'ltr', null, null],
                ['af', 'afr', 'Afrikaans', 'Afrikaans', 'ltr', 2, 'n != 1'],
                ['ak', 'aka', 'Akan', 'Akan', 'ltr', 2, 'n > 1)'],
                ['am', 'amh', 'Amharic', '????????????', 'ltr', 2, 'n > 1'],
                ['an', 'arg', 'Aragonese', 'Aragon??s', 'ltr', 2, 'n != 1'],
                ['ar', 'ara', 'Arabic', '?????????????????', 'rtl', 6, 'n==0 ? 0 : (n==1 ? 1 : (n==2 ? 2 : (n%100>=3 && n%100<=10 ? 3 : (n%100>=11 ? 4 : 5))))'],
                ['as', 'asm', 'Assamese', '?????????????????????', 'ltr', null, null],
                ['av', 'ava', 'Avaric', '???????? ????????', 'ltr', null, null],
                ['ay', 'aym', 'Aymara', 'Aymar aru', 'ltr', 1, '0'],
                ['az', 'aze', 'Azerbaijani', 'Az??rbaycan dili', 'ltr', 2, 'n != 1'],

                ['ba', 'bak', 'Bashkir', '?????????????? ????????', 'ltr', null, null],
                ['be', 'bel', 'Belarusian', '????????????????????', 'ltr', 3, 'n%10==1 && n%100!=11 ? 0 : (n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'],
                ['bg', 'bul', 'Bulgarian', '?????????????????? ????????', 'ltr', 2, 'n != 1'],
                ['bh', 'bih', 'Bihari languages', '?????????????????????', 'ltr', null, null],
                ['bi', 'bis', 'Bislama', 'Bislama', 'ltr', null, null],
                ['bm', 'bam', 'Bambara', 'Bamanankan', 'ltr', null, null],
                ['bn', 'ben', 'Bengali', '???????????????', 'ltr', 2, 'n != 1'],
                ['bo', 'tib', 'Tibetan', '?????????????????????', 'ltr', 1, '0'],
                ['br', 'bre', 'Breton', 'Brezhoneg', 'ltr', 2, 'n > 1'],
                ['bs', 'bos', 'Bosnian', 'Bosanski jezik', 'ltr', 3, 'n%10==1 && n%100!=11 ? 0 : (n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'],

                ['ca', 'cat', 'Catalan', 'Catal??', 'ltr', 2, 'n != 1'],
                ['ce', 'che', 'Chechen', '?????????????? ????????', 'ltr', null, null],
                ['ch', 'cha', 'Chamorro', 'Chamoru', 'ltr', 3, 'n==1 ? 0 : ((n>=2 && n<=4) ? 1 : 2)'],
                ['co', 'cos', 'Corsican', 'Corsu', 'ltr', null, null],
                ['cr', 'cre', 'Cree', '?????????????????????', 'ltr', null, null],
                ['cs', 'cze', 'Czech', '??esky', 'ltr', null, null],
                ['cu', 'chu', 'Church Slavonic', '?????????? ????????????????????', 'ltr', null, null],
                ['cv', 'chv', 'Chuvash', '?????????? ??????????', 'ltr', null, null],
                ['cy', 'wel', 'Welsh', 'Cymraeg', 'ltr', 4, 'n==1 ? 0 : ((n==2) ? 1 : ((n != 8 && n != 11) ? 2 : 3))'],

                ['da', 'dan', 'Danish', 'Dansk', 'ltr', 2, 'n != 1'],
                ['de', 'ger', 'German', 'Deutsch', 'ltr', 2, 'n != 1'],
                ['dv', 'div', 'Maldivian', '????????????', 'rtl', null, null],
                ['dz', 'dzo', 'Dzongkha', '??????????????????', 'ltr', 1, '0'],

                ['ee', 'ewe', 'Ewe', '??????gb??', 'ltr', null, null],
                ['el', 'gre', 'Greek', '????????????????', 'ltr', 2, 'n != 1'],
                ['en', 'eng', 'English', 'English', 'ltr', 2, 'n != 1'],
                ['eo', 'epo', 'Esperanto', 'Esperanto', 'ltr', 2, 'n != 1'],
                ['es', 'spa', 'Spanish', 'Espa??ol', 'ltr', 2, 'n != 1'],
                ['es-ar', null, 'Argentinean Spanish', 'Argentinean Spanish', 'ltr', 2, 'n != 1'],
                ['et', 'est', 'Estonian', 'Eesti keel', 'ltr', 2, 'n != 1'],
                ['eu', 'baq', 'Basque', 'Euskara', 'ltr', 2, 'n != 1'],

                ['fa', 'per', 'Persian', '?????????????', 'rtl', 1, '0'],
                ['ff', 'ful', 'Fulah', 'Fulfulde', 'ltr', 2, 'n != 1'],
                ['fi', 'fin', 'Finnish', 'Suomen kieli', 'ltr', 2, 'n != 1'],
                ['fj', 'fij', 'Fijian', 'Vosa Vakaviti', 'ltr', null, null],
                ['fo', 'fao', 'Faroese', 'F??royskt', 'ltr', 2, 'n != 1'],
                ['fr', 'fre', 'French', 'Fran??ais', 'ltr', 2, 'n > 1'],
                ['fy', 'fry', 'Western Frisian', 'Frysk', 'ltr', 2, 'n != 1'],

                ['ga', 'gle', 'Irish', 'Gaeilge', 'ltr', 5, 'n==1 ? 0 : (n==2 ? 1 : (n<7 ? 2 : (n<11 ? 3 : 4)))'],
                ['gd', 'gla', 'Gaelic', 'G??idhlig', 'ltr', 4, '(n==1 || n==11) ? 0 : ((n==2 || n==12) ? 1 : ((n > 2 && n < 20) ? 2 : 3))'],
                ['gl', 'glg', 'Galician', 'Galego', 'ltr', 2, 'n != 1'],
                ['gn', 'grn', 'Guarani', "Ava??e'???", 'ltr', null, null],
                ['gu', 'guj', 'Gujarati', '?????????????????????', 'ltr', 2, 'n != 1'],
                ['gv', 'glv', 'Manx', 'Ghaelg', 'ltr', null, null],

                ['ha', 'hau', 'Hausa', '???????????????', 'rtl', 2, 'n != 1'],
                ['he', 'heb', 'Hebrew', '?????????????', 'rtl', 2, 'n != 1'],
                ['hi', 'hin', 'Hindi', '??????????????????', 'ltr', 2, 'n != 1'],
                ['ho', 'hmo', 'Hiri Motu', 'Hiri Motu', 'ltr', null, null],
                ['hr', 'hrv', 'Croatian', 'Hrvatski', 'ltr', 3, 'n%10==1 && n%100!=11 ? 0 : (n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'],
                ['ht', 'hat', 'Haitian', 'Krey??l ayisyen', 'ltr', null, null],
                ['hu', 'hun', 'Hungarian', 'Magyar', 'ltr', 2, 'n != 1'],
                ['hy', 'arm', 'Armenian', '??????????????', 'ltr', 2, 'n != 1'],
                ['hz', 'her', 'Herero', 'Otjiherero', 'ltr', null, null],

                ['ia', 'ina', 'Interlingua', 'Interlingua', 'ltr', 2, 'n != 1'],
                ['id', 'ind', 'Indonesian', 'Bahasa Indonesia', 'ltr', 1, '0'],
                ['ie', 'ile', 'Interlingue', 'Interlingue', 'ltr', null, null],
                ['ig', 'ibo', 'Igbo', 'Igbo', 'ltr', null, null],
                ['ii', 'iii', 'Sichuan Yi', '??????', 'ltr', null, null],
                ['ik', 'ipk', 'Inupiaq', 'I??upiaq', 'ltr', null, null],
                ['io', 'ido', 'Ido', 'Ido', 'ltr', null, null],
                ['is', 'ice', 'Icelandic', '??slenska', 'ltr', 2, '(n%10!=1 || n%100==11) ? 1 : 0'],
                ['it', 'ita', 'Italian', 'Italiano', 'ltr', 2, 'n != 1'],
                ['iu', 'iku', 'Inuktitut', '??????????????????', 'ltr', null, null],

                ['ja', 'jpn', 'Japanese', '?????????', 'ltr', 1, '0'],
                ['jv', 'jav', 'Javanese', 'Basa Jawa', 'ltr', 2, 'n != 0'],

                ['ka', 'geo', 'Georgian', '?????????????????????', 'ltr', 1, '0'],
                ['kg', 'kon', 'Kongo', 'KiKongo', 'ltr', null, null],
                ['ki', 'kik', 'Kikuyu', 'G??k??y??', 'ltr', null, null],
                ['kj', 'kua', 'Kuanyama', 'Kuanyama', 'ltr', null, null],
                ['kk', 'kaz', 'Kazakh', '?????????? ????????', 'ltr', 1, '0'],
                ['kl', 'kal', 'Greenlandic', 'Kalaallisut', 'ltr', null, null],
                ['km', 'khm', 'Central Khmer', '???????????????????????????', 'ltr', 1, '0'],
                ['kn', 'kan', 'Kannada', '???????????????', 'ltr', 2, 'n != 1'],
                ['ko', 'kor', 'Korean', '?????????', 'ltr', 1, '0'],
                ['kr', 'kau', 'Kanuri', 'Kanuri', 'ltr', null, null],
                ['ks', 'kas', 'Kashmiri', '?????????????????????', 'rtl', null, null],
                ['ku', 'kur', 'Kurdish', 'Kurd??', 'ltr', 2, 'n!= 1'],
                ['kv', 'kom', 'Komi', '???????? ??????', 'ltr', null, null],
                ['kw', 'cor', 'Cornish', 'Kernewek', 'ltr', 4, 'n==1 ? 0 : ((n==2) ? 1 : ((n == 3) ? 2 : 3))'],
                ['ky', 'kir', 'Kirghiz', '???????????? ????????', 'ltr', 1, '0'],

                ['la', 'lat', 'Latin', 'Latine', 'ltr', null, null],
                ['lb', 'ltz', 'Luxembourgish', 'L??tzebuergesch', 'ltr', 2, 'n != 1'],
                ['lg', 'lug', 'Ganda', 'Luganda', 'ltr', null, null],
                ['li', 'lim', 'Limburgan', 'Limburgs', 'ltr', null, null],
                ['ln', 'lin', 'Lingala', 'Ling??la', 'ltr', 2, 'n>1'],
                ['lo', 'lao', 'Lao', '?????????????????????', 'ltr', 1, '0'],
                ['lt', 'lit', 'Lithuanian', 'Lietuvi?? kalba', 'ltr', 3, 'n%10==1 && n%100!=11 ? 0 : (n%10>=2 && (n%100<10 or n%100>=20) ? 1 : 2)'],
                ['lu', 'lub', 'Luba-Katanga', 'Luba-Katanga', 'ltr', null, null],
                ['lv', 'lav', 'Latvian', 'Latvie??u valoda', 'ltr', 3, 'n%10==1 && n%100!=11 ? 0 : (n != 0 ? 1 : 2)'],

                ['mg', 'mlg', 'Malagasy', 'Malagasy fiteny', 'ltr', 2, 'n > 1'],
                ['mh', 'mah', 'Marshallese', 'Kajin M??aje??', 'ltr', null, null],
                ['mi', 'mao', 'Maori', 'Te reo M??ori', 'ltr', 2, 'n > 1'],
                ['mk', 'mac', 'Macedonian', '???????????????????? ??????????', 'ltr', 2, 'n==1 || n%10==1 ? 0 : 1'],
                ['ml', 'mal', 'Malayalam', '??????????????????', 'ltr', 2, 'n != 1'],
                ['mn', 'mon', 'Mongolian', '????????????', 'ltr', 2, 'n != 1'],
                ['mo', null, 'Moldavian', 'Limba moldoveneasc??', 'ltr', 3, 'n==1 ? 0 : ((n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2)'], //cf: ro
                ['mr', 'mar', 'Marathi', '???????????????', 'ltr', 2, 'n != 1'],
                ['ms', 'may', 'Malay', 'Bahasa Melayu', 'ltr', 1, '0'],
                ['mt', 'mlt', 'Maltese', 'Malti', 'ltr', 4, 'n==1 ? 0 : (n==0 || ( n%100>1 && n%100<11) ? 1 : ((n%100>10 && n%100<20 ) ? 2 : 3))'],
                ['my', 'bur', 'Burmese', '???????????????', 'ltr', 1, '0'],

                ['na', 'nau', 'Nauru', 'Ekakair?? Naoero', 'ltr', null, null],
                ['nb', 'nob', 'Norwegian Bokm??l', 'Norsk bokm??l', 'ltr', 2, 'n != 1'],
                ['nd', 'nde', 'North Ndebele', 'isiNdebele', 'ltr', null, null],
                ['ne', 'nep', 'Nepali', '??????????????????', 'ltr', 2, 'n != 1'],
                ['ng', 'ndo', 'Ndonga', 'Owambo', 'ltr', null, null],
                ['nl', 'dut', 'Flemish', 'Nederlands', 'ltr', 2, 'n != 1'],
                ['nl-be', null, 'Flemish', 'Nederlands (Belgium)', 'ltr', 2, 'n != 1'],
                ['nn', 'nno', 'Norwegian Nynorsk', 'Norsk nynorsk', 'ltr', 2, 'n != 1'],
                ['no', 'nor', 'Norwegian', 'Norsk', 'ltr', 2, 'n != 1'],
                ['nr', 'nbl', 'South Ndebele', 'Nd??b??l??', 'ltr', null, null],
                ['nv', 'nav', 'Navajo', 'Din?? bizaad', 'ltr', null, null],
                ['ny', 'nya', 'Chichewa', 'ChiChe??a', 'ltr', null, null],

                ['oc', 'oci', 'Occitan', 'Occitan', 'ltr', 2, 'n > 1'],
                ['oj', 'oji', 'Ojibwa', '????????????????????????', 'ltr', null, null],
                ['om', 'orm', 'Oromo', 'Afaan Oromoo', 'ltr', null, null],
                ['or', 'ori', 'Oriya', '???????????????', 'ltr', 2, 'n != 1'],
                ['os', 'oss', 'Ossetian', '???????? ??????????', 'ltr', null, null],

                ['pa', 'pan', 'Panjabi', '??????????????????', 'ltr', 2, 'n != 1'],
                ['pi', 'pli', 'Pali', '????????????', 'ltr', null, null],
                ['pl', 'pol', 'Polish', 'Polski', 'ltr', 3, 'n==1 ? 0 : (n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'],
                ['ps', 'pus', 'Pushto', '???????????', 'rtl', 2, 'n != 1'],
                ['pt', 'por', 'Portuguese', 'Portugu??s', 'ltr', 2, 'n != 1'],
                ['pt-br', null, 'Brazilian Portuguese', 'Portugu??s do Brasil', 'ltr', 2, 'n > 1'],

                ['qu', 'que', 'Quechua', 'Runa Simi', 'ltr', null, null],

                ['rm', 'roh', 'Romansh', 'Rumantsch grischun', 'ltr', 2, 'n != 1'],
                ['rn', 'run', 'Rundi', 'kiRundi', 'ltr', null, null],
                ['ro', 'rum', 'Romanian', 'Rom??n??', 'ltr', 3, 'n==1 ? 0 : ((n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2)'],
                ['ru', 'rus', 'Russian', '??????????????', 'ltr', 3, 'n%10==1 && n%100!=11 ? 0 : (n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'],
                ['rw', 'kin', 'Kinyarwanda', 'IKinyarwanda', 'ltr', 2, 'n != 1'],

                ['sa', 'san', 'Sanskrit', '???????????????????????????', 'ltr', null, null],
                ['sc', 'srd', 'Sardinian', 'sardu', 'ltr', null, null],
                ['sd', 'snd', 'Sindhi', '??????????????????', 'ltr', 2, 'n != 1'],
                ['se', 'sme', 'Northern Sami', 'Davvis??megiella', 'ltr', null, null],
                ['sg', 'sag', 'Sango', 'Y??ng?? t?? s??ng??', 'ltr', null, null],
                ['sh', null, null, 'SrpskoHrvatski', 'ltr', null, null], //!
                ['si', 'sin', 'Sinhalese', '???????????????', 'ltr', 2, 'n != 1'],
                ['sk', 'slo', 'Slovak', 'Sloven??ina', 'ltr', 3, '(n==1) ? 0 : ((n>=2 && n<=4) ? 1 : 2)'],
                ['sl', 'slv', 'Slovenian', 'Sloven????ina', 'ltr', 4, 'n%100==1 ? 1 : (n%100==2 ? 2 : (n%100==3 || n%100==4 ? 3 : 0))'],
                ['sm', 'smo', 'Samoan', "Gagana fa'a Samoa", 'ltr', null, null],
                ['sn', 'sna', 'Shona', 'chiShona', 'ltr', null, null],
                ['so', 'som', 'Somali', 'Soomaaliga', 'ltr', 2, 'n != 1'],
                ['sq', 'alb', 'Albanian', 'Shqip', 'ltr', 2, 'n != 1'],
                ['sr', 'srp', 'Serbian', '???????????? ??????????', 'ltr', 3, 'n%10==1 && n%100!=11 ? 0 : (n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'],
                ['ss', 'ssw', 'Swati', 'SiSwati', 'ltr', null, null],
                ['st', 'sot', 'Southern Sotho', 'seSotho', 'ltr', null, null],
                ['su', 'sun', 'Sundanese', 'Basa Sunda', 'ltr', 1, '0'],
                ['sv', 'swe', 'Swedish', 'Svenska', 'ltr', 2, 'n != 1'],
                ['sw', 'swa', 'Swahili', 'Kiswahili', 'ltr', 2, 'n != 1'],

                ['ta', 'tam', 'Tamil', '???????????????', 'ltr', 2, 'n != 1'],
                ['te', 'tel', 'Telugu', '??????????????????', 'ltr', 2, 'n != 1'],
                ['tg', 'tgk', 'Tajik', '????????????', 'ltr', 2, 'n > 1'],
                ['th', 'tha', 'Thai', '?????????', 'ltr', 1, '0'],
                ['ti', 'tir', 'Tigrinya', '????????????', 'ltr', 2, 'n > 1'],
                ['tk', 'tuk', 'Turkmen', 'T??rkmen', 'ltr', 2, 'n != 1'],
                ['tl', 'tlg', 'Tagalog', 'Tagalog', 'ltr', null, null],
                ['tn', 'tsn', 'Tswana', 'seTswana', 'ltr', null, null],
                ['to', 'ton', 'Tonga', 'faka Tonga', 'ltr', null, null],
                ['tr', 'tur', 'Turkish', 'T??rk??e', 'ltr', 2, 'n > 1'],
                ['ts', 'tso', 'Tsonga', 'xiTsonga', 'ltr', null, null],
                ['tt', 'tat', 'Tatar', '??????????????', 'ltr', 1, '0'],
                ['tw', 'twi', 'Twi', 'Twi', 'ltr', null, null],
                ['ty', 'tah', 'Tahitian', 'Reo M??`ohi', 'ltr', null, null],

                ['ug', 'uig', 'Uighur', 'Uy??urq??', 'ltr', 1, '0'],
                ['uk', 'ukr', 'Ukrainian', '????????????????????', 'ltr', 3, 'n%10==1 && n%100!=11 ? 0 : (n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'],
                ['ur', 'urd', 'Urdu', '???????????', 'rtl', 2, 'n != 1'],
                ['uz', 'uzb', 'Uzbek', "O'zbek", 'ltr', 2, 'n > 1'],

                ['ve', 'ven', 'Venda', 'tshiVen???a', 'ltr', null, null],
                ['vi', 'vie', 'Vietnamese', 'Ti???ng Vi???t', 'ltr', 1, '0'],
                ['vo', 'vol', 'Volap??k', 'Volap??k', 'ltr', null, null],

                ['wa', 'wln', 'Walloon', 'Walon', 'ltr', 2, 'n > 1'],
                ['wo', 'wol', 'Wolof', 'Wollof', 'ltr', 1, '0'],

                ['xh', 'xho', 'Xhosa', 'isiXhosa', 'ltr', null, null],

                ['yi', 'yid', 'Yiddish', '???????????????', 'rtl', null, null],
                ['yo', 'yor', 'Yoruba', 'Yor??b??', 'ltr', 2, 'n != 1'],

                ['za', 'zha', 'Chuang', 'Sa?? cue????', 'ltr', null, null],
                ['zh-cn', 'zhi', 'Chinese', '??????', 'ltr', 1, '0'],
                ['zh-hk', null, 'Honk Kong Chinese', '?????? (??????)', 'ltr', 1, '0'],
                ['zh-tw', null, 'Taiwan Chinese', '?????? (??????)', 'ltr', 1, '0'],
                ['zu', 'zul', 'Zulu', 'isiZulu', 'ltr', null, null],
            ];
        }

        $r = [];
        foreach (self::$languages_definitions as $_) {
            $r[$_[0]] = empty($_[$type]) ? $default : $_[$type];
        }

        return $r;
    }
    //@}
}
