<?php
/**
 * @class wiki2xhtml
 *
 * @package Clearbricks
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */

/*
Contributor(s):
Stephanie Booth
Mathieu Pillard
Christophe Bonijol
Jean-Charles Bagneris
Nicolas Chachereau
Jérôme Lipowicz
Franck Paul

Version : 3.2.24
Release date : 2021-10-25

History :

3.2.24 - Franck
=> Ajout support bloc détail (|summary en première ligne du bloc, | en dernière ligne du bloc, contenu du bloc libre)

3.2.23 - Franck
=> Ajout support attributs supplémentaires (§attributs§) pour les éléments en ligne (sans d'imbrication)
=> Ajout support ;;span;;

3.2.22 - Franck
=> Ajout support attributs supplémentaires (§§attributs[|attributs parent]§§ en fin de 1re ligne) pour les blocs
=> Ajout support ,,indice,,

3.2.21 - Franck
=> Suppression du support _indice_ (conflit fréquent avec les noms de fichier/URL/…)

3.2.20 - Franck
=> Suppression des p entourant les figures ou les liens incluants une figure

3.2.19 - Franck
=> abbr, img, em, strong, i, code, del, ins, mark, sup are only elements converted inside a link text

3.2.18 - Franck
=> Def lists required at least a space after : or =

3.2.17 - Franck
=> Added ££text|lang££ support which gives an <i>…</i>

3.2.16 - Franck
=> Added _indice_ support

3.2.15 - Franck
=> Added ^exponant^ support

3.2.14 - Franck
=> Ajout de la gestion d'un fichier externe d'acronymes (fusionné avec le fichier existant)

3.2.13 - Franck
=> Added = <term>, : <definition> support (definition list)

3.2.12 - Franck
=> PHP 7.2 compliance

3.2.11 - Franck
=> Added ) aside block support (HTML5 only)

3.2.10 - Franck
=> Added ""marked text"" support (HTML5 only)

3.2.9 - Franck
=> <a name="anchor"></a> est remplacé par <a id="anchor"></a> pour assurer la compatibilité avec HTML5

3.2.8 - Franck
=> <acronym> est remplacé par <abbr> pour assurer la compatibilité avec HTML5

3.2.7 - Franck
=> Les styles d'alignement des images sont modifiables via les options

3.2.6 - Franck
=> Added ``inline html`` support

3.2.5 - Franck
=> Changed longdesc by title in images

3.2.4 - Olivier
=> Auto links
=> Code cleanup

3.2.3 - Olivier
=> PHP5 Strict

3.2.2 - Olivier
=> Changement de la gestion des URL spéciales

3.2.1 - Olivier
=> Changement syntaxe des macros

3.2 - Olivier
=> Changement de fonctionnement des macros
=> Passage de fonctions externes pour les macros et les mots wiki

3.1d - Jérôme Lipowicz
=> antispam
- Olivier
=> centrage d'image

3.1c - Olivier
=> Possibilité d'échaper les | dans les marqueurs avec \

3.1b - Nicolas Chachereau
=> Changement de regexp pour la correction syntaxique

3.1a - Olivier
=> Bug du Call-time pass-by-reference

3.1 - Olivier
=> Ajout des macros «««..»»»
=> Ajout des blocs vides øøø
=> Ajout du niveau de titre paramétrable
=> Option de blocage du parseur dans les <pre>
=> Titres au format setext (experimental, désactivé)

3.0 - Olivier
=> Récriture du parseur inline, plus d'erreur XHTML
=> Ajout d'une vérification d'intégrité pour les listes
=> Les acronymes sont maintenant dans un fichier texte
=> Ajout d'un tag images ((..)), del --..-- et ins ++..++
=> Plus possible de faire des liens JS [lien|javascript:...]
=> Ajout des notes de bas de page §§...§§
=> Ajout des mots wiki

2.5 - Olivier
=> Récriture du code, plus besoin du saut de ligne entre blocs !=

2.0 - Stephanie
=> correction des PCRE et ajout de fonctionnalités
- Mathieu
=> ajout du strip-tags, implementation des options, reconnaissance automatique d'url, etc.
- Olivier
=> changement de active_link en active_urls
=> ajout des options pour les blocs
=> intégration de l'aide dans le code, avec les options
=> début de quelque chose pour la reconnaissance auto d'url (avec Mat)
 */

class wiki2xhtml
{
    public $__version__ = '3.2.23';

    public $T;
    public $opt;
    public $line;
    public $acro_table;
    public $foot_notes;
    public $macros;
    public $functions;

    public $tags;
    public $linetags;
    public $open_tags;
    public $close_tags;
    public $custom_tags = [];
    public $all_tags;
    public $tag_pattern;
    public $escape_table;
    public $allowed_inline = [];

    public function __construct()
    {
        # Mise en place des options
        $this->setOpt('active_title', 1); # Activation des titres !!!
        $this->setOpt('active_setext_title', 0); # Activation des titres setext (EXPERIMENTAL)
        $this->setOpt('active_hr', 1); # Activation des <hr />
        $this->setOpt('active_lists', 1); # Activation des listes
        $this->setOpt('active_defl', 1); # Activation des listes de définition
        $this->setOpt('active_quote', 1); # Activation du <blockquote>
        $this->setOpt('active_pre', 1); # Activation du <pre>
        $this->setOpt('active_empty', 1); # Activation du bloc vide øøø
        $this->setOpt('active_auto_urls', 0); # Activation de la reconnaissance d'url
        $this->setOpt('active_auto_br', 0); # Activation du saut de ligne automatique (dans les paragraphes)
        $this->setOpt('active_antispam', 1); # Activation de l'antispam pour les emails
        $this->setOpt('active_urls', 1); # Activation des liens []
        $this->setOpt('active_auto_img', 1); # Activation des images automatiques dans les liens []
        $this->setOpt('active_img', 1); # Activation des images (())
        $this->setOpt('active_anchor', 1); # Activation des ancres ~...~
        $this->setOpt('active_em', 1); # Activation du <em> ''...''
        $this->setOpt('active_strong', 1); # Activation du <strong> __...__
        $this->setOpt('active_br', 1); # Activation du <br /> %%%
        $this->setOpt('active_q', 1); # Activation du <q> {{...}}
        $this->setOpt('active_code', 1); # Activation du <code> @@...@@
        $this->setOpt('active_acronym', 1); # Activation des acronymes
        $this->setOpt('active_ins', 1); # Activation des <ins> ++..++
        $this->setOpt('active_del', 1); # Activation des <del> --..--
        $this->setOpt('active_inline_html', 1); # Activation du HTML inline ``...``
        $this->setOpt('active_footnotes', 1); # Activation des notes de bas de page
        $this->setOpt('active_wikiwords', 0); # Activation des mots wiki
        $this->setOpt('active_macros', 1); # Activation des macros /// ///
        $this->setOpt('active_mark', 1); # Activation des <mark> ""..""
        $this->setOpt('active_aside', 1); # Activation du <aside>
        $this->setOpt('active_sup', 1); # Activation du <sup> ^..^
        $this->setOpt('active_sub', 1); # Activation du <sub> ,,..,,
        $this->setOpt('active_i', 1); # Activation du <i> ££..££
        $this->setOpt('active_span', 1); # Activation du <span> ;;..;;
        $this->setOpt('active_details', 1); #activation du <details> |sommaire ...

        $this->setOpt('parse_pre', 1); # Parser l'intérieur de blocs <pre> ?

        $this->setOpt('active_fr_syntax', 1); # Corrections syntaxe FR

        $this->setOpt('first_title_level', 3); # Premier niveau de titre <h..>

        $this->setOpt('note_prefix', 'wiki-footnote');
        $this->setOpt('note_str', '<div class="footnotes"><h4>Notes</h4>%s</div>');
        $this->setOpt('note_str_single', '<div class="footnotes"><h4>Note</h4>%s</div>');
        $this->setOpt(
            'words_pattern',
            '((?<![A-Za-z0-9])([A-Z][a-z]+){2,}(?![A-Za-z0-9]))'
        );

        $this->setOpt(
            'auto_url_pattern',
            '%(?<![\[\|])(http://|https://|ftp://|news:)([^"\s\)!]+)%msu'
        );

        $this->setOpt('acronyms_file', __DIR__ . '/acronyms.txt');

        $this->setOpt('img_style_left', 'float:left; margin: 0 1em 1em 0;');
        $this->setOpt('img_style_center', 'display:block; margin:0 auto;');
        $this->setOpt('img_style_right', 'float:right; margin: 0 0 1em 1em;');

        $this->acro_table = $this->__getAcronyms();
        $this->foot_notes = [];
        $this->functions  = [];
        $this->macros     = [];

        $this->registerFunction('macro:html', [$this, '__macroHTML']);
    }

    public function setOpt(string $option, $value)
    {
        $this->opt[$option] = $value;

        if ($option == 'acronyms_file' && isset($this->opt[$option]) && file_exists($this->opt[$option])) {
            // Parse and merge new acronyms
            $this->acro_table = array_merge($this->acro_table, $this->__getAcronyms());
        }
    }

    public function setOpts($options): void
    {
        if (!is_array($options)) {
            return;
        }

        foreach ($options as $k => $v) {
            $this->opt[$k] = $v;
        }
    }

    public function getOpt(string $option)
    {
        return (!empty($this->opt[$option])) ? $this->opt[$option] : false;
    }

    public function registerFunction(string $type, $name)
    {
        if (is_callable($name)) {
            $this->functions[$type] = $name;
        }
    }

    public function transform(string $in): string
    {
        # Initialisation des tags
        $this->__initTags();
        $this->foot_notes = [];

        # Récupération des macros
        if ($this->getOpt('active_macros')) {
            $in = preg_replace_callback('#^///(.*?)///($|\r)#ms', [$this, '__getMacro'], $in);
        }

        # Vérification du niveau de titre
        if ($this->getOpt('first_title_level') > 4) {
            $this->setOpt('first_title_level', 4);
        }

        $res = str_replace("\r", '', $in);

        $escape_pattern = [];

        # traitement des titres à la setext
        if ($this->getOpt('active_setext_title') && $this->getOpt('active_title')) {
            $res = preg_replace('/^(.*)\n[=]{5,}$/m', '!!!$1', $res);
            $res = preg_replace('/^(.*)\n[-]{5,}$/m', '!!$1', $res);
        }

        # Transformation des mots Wiki
        if ($this->getOpt('active_wikiwords') && $this->getOpt('words_pattern')) {
            $res = preg_replace('/' . $this->getOpt('words_pattern') . '/msu', '¶¶¶$1¶¶¶', $res);
        }

        # Transformation des URLs automatiques
        if ($this->getOpt('active_auto_urls')) {
            $active_urls = $this->getOpt('active_urls');

            $this->setOpt('active_urls', 1);
            $this->__initTags();

            # If urls are not active, escape URLs tags
            if (!$active_urls) {
                $res = preg_replace(
                    '%(?<!\\\\)([' . preg_quote(implode('', $this->tags['a'])) . '])%msU',
                    '\\\$1',
                    $res
                );
            }

            # Transforms urls while preserving tags.
            $tree = preg_split($this->tag_pattern, $res, -1, PREG_SPLIT_DELIM_CAPTURE);
            foreach ($tree as &$leaf) {
                $leaf = preg_replace($this->getOpt('auto_url_pattern'), '[$1$2]', $leaf);
            }
            unset($leaf);
            $res = implode($tree);
        }

        $this->T   = explode("\n", $res);
        $this->T[] = '';

        # Parse les blocs
        $res = $this->__parseBlocks();

        # Line break
        if ($this->getOpt('active_br')) {
            $res              = preg_replace('/(?<!\\\)%%%/', '<br />', $res);
            $escape_pattern[] = '%%%';
        }

        # Nettoyage des \s en trop
        $res = preg_replace('/([\s]+)(<\/p>|<\/li>|<\/pre>)/u', '$2', $res);
        $res = preg_replace('/(<li>)([\s]+)/u', '$1', $res);

        # On vire les escapes
        if (!empty($escape_pattern)) {
            $res = preg_replace('/\\\(' . implode('|', $escape_pattern) . ')/', '$1', $res);
        }

        # On vire les ¶¶¶MotWiki¶¶¶ qui sont resté (dans les url...)
        if ($this->getOpt('active_wikiwords') && $this->getOpt('words_pattern')) {
            $res = preg_replace('/¶¶¶' . $this->getOpt('words_pattern') . '¶¶¶/msu', '$1', $res);
        }

        # On remet les macros
        if ($this->getOpt('active_macros')) {
            $res = preg_replace_callback('/^##########MACRO#([0-9]+)#$/ms', [$this, '__putMacro'], $res);
        }

        # Auto line break dans les paragraphes
        if ($this->getOpt('active_auto_br')) {
            $res = preg_replace_callback('%(<p>)(.*?)(</p>)%msu', [$this, '__autoBR'], $res);
        }

        # Remove wrapping p around figure
        # Adapted from https://micahjon.com/2016/removing-wrapping-p-paragraph-tags-around-images-wordpress/
        $ret = $res;
        while (preg_match('/<p>((?:.(?!p>))*?)(<a[^>]*>)?\s*(<figure[^>]*>)(.*?)(<\/figure>)\s*(<\/a>)?(.*?)<\/p>/msu', $ret)) {
            $ret = preg_replace_callback(
                '/<p>((?:.(?!p>))*?)(<a[^>]*>)?\s*(<figure[^>]*>)(.*?)(<\/figure>)\s*(<\/a>)?(.*?)<\/p>/msu',
                function ($matches) {
                    $figure = $matches[2] . $matches[3] . $matches[4] . $matches[5] . $matches[6];
                    $before = trim((string) $matches[1]);
                    if ($before) {
                        $before = '<p>' . $before . '</p>';
                    }
                    $after = trim((string) $matches[7]);
                    if ($after) {
                        $after = '<p>' . $after . '</p>';
                    }

                    return $before . $figure . $after;
                },
                $ret
            );
        }
        if (!is_null($ret)) {
            $res = $ret;
        }

        # On ajoute les notes
        if (count($this->foot_notes) > 0) { // @phpstan-ignore-line
            $res_notes = '';
            $i         = 1;
            foreach ($this->foot_notes as $k => $v) {
                $res_notes .= "\n" . '<p>[<a href="#rev-' . $k . '" id="' . $k . '">' . $i . '</a>] ' . $v . '</p>';
                $i++;
            }
            $res .= sprintf("\n" . (count($this->foot_notes) > 1 ? $this->getOpt('note_str') : $this->getOpt('note_str_single')) . "\n", $res_notes);
        }

        return $res;
    }

    /* PRIVATE
    --------------------------------------------------- */

    private function __initTags()
    {
        $tags = [
            'em'     => ["''", "''"],
            'strong' => ['__', '__'],
            'abbr'   => ['??', '??'],
            'a'      => ['[', ']'],
            'img'    => ['((', '))'],
            'q'      => ['{{', '}}'],
            'code'   => ['@@', '@@'],
            'anchor' => ['~', '~'],
            'del'    => ['--', '--'],
            'ins'    => ['++', '++'],
            'inline' => ['``', '``'],
            'note'   => ['$$', '$$'],
            'word'   => ['¶¶¶', '¶¶¶'],
            'mark'   => ['""', '""'],
            'sup'    => ['^', '^'],
            'sub'    => [',,', ',,'],
            'i'      => ['££', '££'],
            'span'   => [';;', ';;'],
        ];
        $this->linetags = [
            'empty'   => 'øøø',
            'title'   => '([!]{1,4})',
            'hr'      => '[-]{4}[- ]',
            'quote'   => '(&gt;|;:)',
            'lists'   => '([*#]+)',
            'defl'    => '([=|:]{1} )',
            'pre'     => '[ ]{1}',
            'aside'   => '[\)]{1}',
            'details' => '[\|]{1}',
        ];

        $this->tags = array_merge($tags, $this->custom_tags);

        # Suppression des tags selon les options
        if (!$this->getOpt('active_urls')) {
            unset($this->tags['a']);
        }
        if (!$this->getOpt('active_img')) {
            unset($this->tags['img']);
        }
        if (!$this->getOpt('active_anchor')) {
            unset($this->tags['anchor']);
        }
        if (!$this->getOpt('active_em')) {
            unset($this->tags['em']);
        }
        if (!$this->getOpt('active_strong')) {
            unset($this->tags['strong']);
        }
        if (!$this->getOpt('active_q')) {
            unset($this->tags['q']);
        }
        if (!$this->getOpt('active_code')) {
            unset($this->tags['code']);
        }
        if (!$this->getOpt('active_acronym')) {
            unset($this->tags['abbr']);
        }
        if (!$this->getOpt('active_ins')) {
            unset($this->tags['ins']);
        }
        if (!$this->getOpt('active_del')) {
            unset($this->tags['del']);
        }
        if (!$this->getOpt('active_inline_html')) {
            unset($this->tags['inline']);
        }
        if (!$this->getOpt('active_footnotes')) {
            unset($this->tags['note']);
        }
        if (!$this->getOpt('active_wikiwords')) {
            unset($this->tags['word']);
        }
        if (!$this->getOpt('active_mark')) {
            unset($this->tags['mark']);
        }
        if (!$this->getOpt('active_sup')) {
            unset($this->tags['sup']);
        }
        if (!$this->getOpt('active_sub')) {
            unset($this->tags['sub']);
        }
        if (!$this->getOpt('active_i')) {
            unset($this->tags['i']);
        }
        if (!$this->getOpt('active_span')) {
            unset($this->tags['span']);
        }

        # Suppression des tags de début de ligne selon les options
        if (!$this->getOpt('active_empty')) {
            unset($this->linetags['empty']);
        }
        if (!$this->getOpt('active_title')) {
            unset($this->linetags['title']);
        }
        if (!$this->getOpt('active_hr')) {
            unset($this->linetags['hr']);
        }
        if (!$this->getOpt('active_quote')) {
            unset($this->linetags['quote']);
        }
        if (!$this->getOpt('active_lists')) {
            unset($this->linetags['lists']);
        }
        if (!$this->getOpt('active_defl')) {
            unset($this->linetags['defl']);
        }
        if (!$this->getOpt('active_pre')) {
            unset($this->linetags['pre']);
        }
        if (!$this->getOpt('active_aside')) {
            unset($this->linetags['aside']);
        }
        if (!$this->getOpt('active_details')) {
            unset($this->linetags['details']);
        }

        $this->open_tags   = $this->__getTags();
        $this->close_tags  = $this->__getTags(false);
        $this->all_tags    = $this->__getAllTags();
        $this->tag_pattern = $this->__getTagsPattern();

        $this->escape_table = $this->all_tags;
        array_walk($this->escape_table, function (&$a) {$a = '\\' . $a;});
    }

    private function __getTags(bool $open = true): array
    {
        $res = [];
        foreach ($this->tags as $k => $v) {
            $res[$k] = ($open) ? $v[0] : $v[1];
        }

        return $res;
    }

    private function __getAllTags(): array
    {
        $res = [];
        foreach ($this->tags as $v) {
            $res[] = $v[0];
            $res[] = $v[1];
        }

        return array_values(array_unique($res));
    }

    private function __getTagsPattern(): string
    {
        $res = $this->all_tags;
        array_walk($res, function (&$a) {$a = preg_quote($a, '/');});

        return '/(?<!\\\)(' . implode('|', $res) . ')/';
    }

    /* Blocs
    --------------------------------------------------- */
    private function __parseBlocks(): string
    {
        $mode = $type = $attr = null;
        $res  = '';
        $max  = count($this->T);

        for ($i = 0; $i < $max; $i++) {
            $pre_mode = $mode;
            $pre_type = $type;
            $end      = ($i + 1 == $max);

            $line = $this->__getLine($i, $type, $mode, $attr);

            if ($type != 'pre' || $this->getOpt('parse_pre')) {
                $line = $line ? $this->__inlineWalk($line) : '';
            }

            $res .= $this->__closeLine($type, $mode, $pre_type, $pre_mode);
            $res .= $this->__openLine($type, $mode, $pre_type, $pre_mode, $attr);

            # P dans les blockquotes et les asides
            if (($type == 'blockquote' || $type == 'aside') && trim((string) $line) == '' && $pre_type == $type) {
                $res .= "</p>\n<p>";
            }

            # Correction de la syntaxe FR dans tous sauf pre et hr
            # Sur idée de Christophe Bonijol
            # Changement de regex (Nicolas Chachereau)
            if ($this->getOpt('active_fr_syntax') && $type != null && $type != 'pre' && $type != 'hr') {
                $line = preg_replace('%[ ]+([:?!;\x{00BB}](\s|$))%u', '&nbsp;$1', $line);
                $line = preg_replace('%(\x{00AB})[ ]+%u', '$1&nbsp;', $line);
            }

            $res .= $line;
        }

        return trim($res);
    }

    private function __getLine(int $i, &$type, &$mode, &$attr)
    {
        $pre_type = $type;
        $pre_mode = $mode;
        $type     = $mode     = null;
        $attr     = null;

        if (empty($this->T[$i])) {
            return false;
        }

        $line = htmlspecialchars($this->T[$i], ENT_NOQUOTES);

        # Ligne vide
        if (empty($line)) {
            $type = null;
        } elseif ($this->getOpt('active_empty') && preg_match('/^øøø(.*)$/', $line, $cap)) {
            $type = null;
            $line = trim((string) $cap[1]);
        }
        # Titre
        elseif ($this->getOpt('active_title') && preg_match('/^([!]{1,4})(.*?)(§§(.*)§§)?$/', $line, $cap)) {
            $type = 'title';
            $mode = strlen($cap[1]);
            $line = trim((string) $cap[2]);
            if (isset($cap[4])) {
                $attr = $cap[4];
            }
        }
        # Ligne HR
        elseif ($this->getOpt('active_hr') && preg_match('/^[-]{4}[- ]*?(§§(.*)§§)?$/', $line, $cap)) {
            $type = 'hr';
            $line = null;
            if (isset($cap[2])) {
                $attr = $cap[2];
            }
        }
        # Blockquote
        elseif ($this->getOpt('active_quote') && preg_match('/^(&gt;|;:)(.*?)(§§(.*)§§)?$/', $line, $cap)) {
            $type = 'blockquote';
            $line = trim((string) $cap[2]);
            if (isset($cap[4])) {
                $attr = $cap[4];
            }
        }
        # Liste
        elseif ($this->getOpt('active_lists') && preg_match('/^([*#]+)(.*?)(§§(.*)§§)?$/', $line, $cap)) {
            $type = 'list';
            $mode = $cap[1];
            if (isset($cap[4])) {
                $attr = $cap[4];
            }
            $valid = true;

            # Vérification d'intégrité
            $dl    = ($type != $pre_type) ? 0 : strlen($pre_mode);
            $d     = strlen($mode);
            $delta = $d - $dl;

            if ($delta < 0 && strpos($pre_mode, $mode) !== 0) {
                $valid = false;
            }
            if ($delta > 0 && $type == $pre_type && strpos($mode, $pre_mode) !== 0) {
                $valid = false;
            }
            if ($delta == 0 && $mode != $pre_mode) {
                $valid = false;
            }
            if ($delta > 1) {
                $valid = false;
            }

            if (!$valid) {
                $type = 'p';
                $mode = null;
                $line = '<br />' . $line;
            } else {
                $line = trim((string) $cap[2]);
            }
        } elseif ($this->getOpt('active_defl') && preg_match('/^([=|:]{1}) (.*?)(§§(.*)§§)?$/', $line, $cap)) {
            $type = 'defl';
            $mode = $cap[1];
            $line = trim((string) $cap[2]);
            if (isset($cap[4])) {
                $attr = $cap[4];
            }
        }
        # Préformaté
        elseif ($this->getOpt('active_pre') && preg_match('/^[ ]{1}(.*?)(§§(.*)§§)?$/', $line, $cap)) {
            $type = 'pre';
            $line = $cap[1];
            if (isset($cap[3])) {
                $attr = trim((string) $cap[3]);
            }
        }
        # Aside
        elseif ($this->getOpt('active_aside') && preg_match('/^[\)]{1}(.*?)(§§(.*)§§)?$/', $line, $cap)) {
            $type = 'aside';
            $line = trim((string) $cap[1]);
            if (isset($cap[3])) {
                $attr = $cap[3];
            }
        }
        # Details
        elseif ($this->getOpt('active_details') && preg_match('/^[\|]{1}(.*?)(§§(.*)§§)?$/', $line, $cap)) {
            $type = 'details';
            $line = trim((string) $cap[1]);
            $mode = $line == '' ? '0' : '1';
            if (isset($cap[3])) {
                $attr = $cap[3];
            }
        }
        # Paragraphe
        else {
            $type = 'p';
            if (preg_match('/^\\\((?:(' . implode('|', $this->linetags) . ')).*)$/', $line, $cap)) {
                $line = $cap[1];
            }
            if (preg_match('/^(.*?)(§§(.*)§§)?$/', $line, $cap)) {
                $line = $cap[1];
                if (isset($cap[3])) {
                    $attr = $cap[3];
                }
            }
            $line = trim((string) $line);
        }

        return $line;
    }

    private function __openLine($type, $mode, $pre_type, $pre_mode, $attr = null)
    {
        $open = ($type != $pre_type);

        $attr_parent = $attr_child = '';
        if ($attr) {
            if ($attrs = $this->__splitTagsAttr($attr)) {
                $attr_child  = $attrs[0] ? ' ' . $attrs[0] : '';
                $attr_parent = isset($attrs[1]) ? ' ' . $attrs[1] : '';
            }
        }

        if ($open && $type == 'p') {
            return "\n<p" . $attr_child . '>';
        } elseif ($open && $type == 'blockquote') {
            return "\n<blockquote" . $attr_child . '><p>';
        } elseif (($open || $mode != $pre_mode) && $type == 'title') {
            $fl = $this->getOpt('first_title_level');
            $fl = $fl + 3;
            $l  = $fl - $mode;

            return "\n<h" . ($l) . $attr_child . '>';
        } elseif ($open && $type == 'pre') {
            return "\n<pre" . $attr_child . '>';
        } elseif ($open && $type == 'aside') {
            return "\n<aside" . $attr_child . '><p>';
        } elseif ($open && $type == 'details' && $mode == '0') {
            return "\n</details>";
        } elseif ($open && $type == 'details' && $mode == '1') {
            return "\n<details" . $attr_child . '><summary>';
        } elseif ($open && $type == 'hr') {
            return "\n<hr" . $attr_child . ' />';
        } elseif ($type == 'list') {
            $dl    = ($open) ? 0 : strlen($pre_mode);
            $d     = strlen($mode);
            $delta = $d - $dl;
            $res   = '';

            if ($delta > 0) {
                if (substr($mode, -1, 1) == '*') {
                    $res .= '<ul' . $attr_parent . ">\n";
                } else {
                    $res .= '<ol' . $attr_parent . ">\n";
                }
            } elseif ($delta < 0) {
                $res .= "</li>\n";
                for ($j = 0; $j < abs($delta); $j++) {
                    if (substr($pre_mode, (0 - $j - 1), 1) == '*') {
                        $res .= "</ul>\n</li>\n";
                    } else {
                        $res .= "</ol>\n</li>\n";
                    }
                }
            } else {
                $res .= "</li>\n";
            }

            return $res . '<li' . $attr_child . '>';
        } elseif ($type == 'defl') {
            $res = ($pre_mode !== '=' && $pre_mode !== ':' ? '<dl' . $attr_parent . ">\n" : '');
            if ($pre_mode == '=') {
                $res .= "</dt>\n";
            } elseif ($pre_mode == ':') {
                $res .= "</dd>\n";
            }
            if ($mode == '=') {
                $res .= '<dt' . $attr_child . '>';
            } else {
                $res .= '<dd' . $attr_child . '>';
            }

            return $res;
        }
    }

    private function __closeLine($type, $mode, $pre_type, $pre_mode)
    {
        $close = ($type != $pre_type);

        if ($close && $pre_type == 'p') {
            return "</p>\n";
        } elseif ($close && $pre_type == 'blockquote') {
            return "</p></blockquote>\n";
        } elseif (($close || $mode != $pre_mode) && $pre_type == 'title') {
            $fl = $this->getOpt('first_title_level');
            $fl = $fl + 3;
            $l  = $fl - $pre_mode;

            return '</h' . ($l) . ">\n";
        } elseif ($close && $pre_type == 'pre') {
            return "</pre>\n";
        } elseif ($close && $pre_type == 'aside') {
            return "</p></aside>\n";
        } elseif ($close && $pre_type == 'details' && $pre_mode == '1') {
            return "</summary>\n";
        } elseif ($close && $pre_type == 'list') {
            $res = '';
            for ($j = 0; $j < strlen($pre_mode); $j++) {
                if (substr($pre_mode, (0 - $j - 1), 1) == '*') {
                    $res .= "</li>\n</ul>\n";
                } else {
                    $res .= "</li>\n</ol>\n";
                }
            }

            return $res;
        } elseif ($close && $pre_type == 'defl') {
            $res = '';
            if ($pre_mode == '=') {
                $res .= "</dt>\n</dl>\n";
            } else {
                $res .= "</dd>\n</dl>\n";
            }

            return $res;
        }

        return "\n";
    }

    /* Inline
    --------------------------------------------------- */
    private function __inlineWalk(string $str, $allow_only = null): string
    {
        $tree = preg_split($this->tag_pattern, $str, -1, PREG_SPLIT_DELIM_CAPTURE);

        $res = '';
        for ($i = 0; $i < count($tree); $i++) {
            $attr = '';

            if (in_array($tree[$i], array_values($this->open_tags)) && ($allow_only == null || in_array(array_search($tree[$i], $this->open_tags), $allow_only))) {
                $tag      = array_search($tree[$i], $this->open_tags);
                $tag_type = 'open';

                if (($tidy = $this->__makeTag($tree, $tag, $i, $i, $attr, $tag_type)) !== false) {
                    if ($tag != '') {
                        $res .= '<' . $tag . $attr;
                        $res .= ($tag_type == 'open') ? '>' : ' />';
                    }
                    $res .= $tidy;
                } else {
                    $res .= $tree[$i];
                }
            } else {
                $res .= $tree[$i];
            }
        }

        # Suppression des echappements
        $res = str_replace($this->escape_table, $this->all_tags, $res);

        return $res;
    }

    private function __makeTag(&$tree, &$tag, $position, &$j, &$attr, &$type)
    {
        $res    = '';
        $closed = false;

        $itag = $this->close_tags[$tag];

        # Recherche fermeture
        for ($i = $position + 1; $i < count($tree); $i++) {
            if ($tree[$i] == $itag) {
                $closed = true;

                break;
            }
        }

        # Résultat
        if ($closed) {
            for ($i = $position + 1; $i < count($tree); $i++) {
                if ($tree[$i] != $itag) {
                    $res .= $tree[$i];
                } else {
                    switch ($tag) {
                        case 'a':
                            $res = $this->__parseLink($res, $tag, $attr, $type);

                            break;
                        case 'img':
                            $type = 'close';
                            if (($res = $this->__parseImg($res, $attr, $tag)) !== null) {
                                $type = 'open';
                            }

                            break;
                        case 'abbr':
                            $res = $this->__parseAcronym($res, $attr);

                            break;
                        case 'q':
                            $res = $this->__parseQ($res, $attr);

                            break;
                        case 'i':
                            $res = $this->__parseI($res, $attr);

                            break;
                        case 'anchor':
                            $tag = 'a';
                            $res = $this->__parseAnchor($res, $attr);

                            break;
                        case 'note':
                            $tag = '';
                            $res = $this->__parseNote($res);

                            break;
                        case 'inline':
                            $tag = '';
                            $res = $this->__parseInlineHTML($res);

                            break;
                        case 'word':
                            $res = $this->parseWikiWord($res, $tag, $attr, $type);

                            break;
                        default:
                            $res = $this->__inlineWalk($res);

                            break;
                    }

                    if ($type == 'open' && $tag != '') {
                        $res .= '</' . $tag . '>';
                    }
                    $j = $i;

                    break;
                }

                # Recherche attributs
                if (preg_match('/^(.*?)(§(.*)§)?$/', $res, $cap)) {
                    $res = $cap[1];
                    if (isset($cap[3])) {
                        $attr .= ' ' . $cap[3];
                    }
                }
            }

            return $res;
        }

        return false;
    }

    private function __splitTagsAttr($str)
    {
        $res = preg_split('/(?<!\\\)\|/', $str);

        foreach ($res as $k => $v) {
            $res[$k] = str_replace("\|", '|', $v);
        }

        return $res;
    }

    # Antispam (Jérôme Lipowicz)
    private function __antiSpam(string $str): string
    {
        $encoded = bin2hex($str);
        $encoded = chunk_split($encoded, 2, '%');
        $encoded = '%' . substr($encoded, 0, strlen($encoded) - 1);

        return $encoded;
    }

    private function __parseLink(string $str, &$tag, &$attr, &$type)
    {
        $n_str    = $this->__inlineWalk($str, ['abbr', 'img', 'em', 'strong', 'i', 'code', 'del', 'ins', 'mark', 'sup', 'sub', 'span']);
        $data     = $this->__splitTagsAttr($n_str);
        $no_image = false;

        # Only URL in data
        if (count($data) == 1) {
            $url     = trim($str);
            $content = strlen($url) > 35 ? substr($url, 0, 35) . '...' : $url;
            $lang    = '';
            $title   = $url;
        } elseif (count($data) > 1) {
            $url      = trim((string) $data[1]);
            $content  = $data[0];
            $lang     = (!empty($data[2])) ? $this->protectAttr($data[2], true) : '';
            $title    = (!empty($data[3])) ? $data[3] : '';
            $no_image = (!empty($data[4])) ? (bool) $data[4] : false;
        }

        # Remplacement si URL spéciale
        $this->__specialUrls($url, $content, $lang, $title);

        # On vire les &nbsp; dans l'url
        $url = str_replace('&nbsp;', ' ', $url);

        if (preg_match('/^(.+)[.](gif|jpg|jpeg|png)$/', $url) && !$no_image && $this->getOpt('active_auto_img')) {
            # On ajoute les dimensions de l'image si locale
            # Idée de Stephanie
            $img_size = null;
            if (!preg_match('#[a-zA-Z]+://#', $url)) {
                if (preg_match('#^/#', $url)) {
                    $path_img = $_SERVER['DOCUMENT_ROOT'] . $url;
                } else {
                    $path_img = $url;
                }

                $img_size = @getimagesize($path_img);
            }

            $attr .= ' src="' . $this->protectAttr($this->protectUrls($url)) . '"' .
            $attr .= (count($data) > 1) ? ' alt="' . $this->protectAttr($content) . '"' : ' alt=""';
            $attr .= ($lang) ? ' lang="' . $lang . '"' : '';
            $attr .= ($title) ? ' title="' . $this->protectAttr($title) . '"' : '';
            $attr .= (is_array($img_size)) ? ' ' . $img_size[3] : '';

            $tag  = 'img';
            $type = 'close';

            return;
        }
        if ($this->getOpt('active_antispam') && preg_match('/^mailto:/', $url)) {
            $content = $content == $url ? preg_replace('%^mailto:%', '', $content) : $content;
            $url     = 'mailto:' . $this->__antiSpam(substr($url, 7));
        }

        $attr .= ' href="' . $this->protectAttr($this->protectUrls($url)) . '"';
        $attr .= ($lang) ? ' hreflang="' . $lang . '"' : '';
        $attr .= ($title) ? ' title="' . $this->protectAttr($title) . '"' : '';

        return $content;
    }

    private function __specialUrls(&$url, &$content, &$lang, &$title)
    {
        foreach ($this->functions as $k => $v) {
            if (strpos($k, 'url:') === 0 && strpos($url, substr($k, 4)) === 0) {
                $res = call_user_func($v, $url, $content);

                $url     = $res['url']     ?? $url;
                $content = $res['content'] ?? $content;
                $lang    = $res['lang']    ?? $lang;
                $title   = $res['title']   ?? $title;

                break;
            }
        }
    }

    private function __parseImg(string $str, string &$attr, &$tag)
    {
        $data = $this->__splitTagsAttr($str);

        $alt          = '';
        $current_attr = $attr;
        $align_attr   = '';
        $url          = $data[0];
        if (!empty($data[1])) {
            $alt = $data[1];
        }

        $attr .= ' src="' . $this->protectAttr($this->protectUrls($url)) . '"';
        $attr .= ' alt="' . $this->protectAttr($alt) . '"';

        if (!empty($data[2])) {
            $data[2] = strtoupper($data[2]);
            $style   = '';
            if ($data[2] == 'G' || $data[2] == 'L') {
                $style = $this->getOpt('img_style_left');
            } elseif ($data[2] == 'D' || $data[2] == 'R') {
                $style = $this->getOpt('img_style_right');
            } elseif ($data[2] == 'C') {
                $style = $this->getOpt('img_style_center');
            }
            if ($style != '') {
                $align_attr = ' style="' . $style . '"';
            }
        }

        if (empty($data[4])) {
            $attr .= $align_attr;
        }
        if (!empty($data[3])) {
            $attr .= ' title="' . $this->protectAttr($data[3]) . '"';
        }

        if (!empty($data[4])) {
            $tag = 'figure';
            $img = '<img' . $attr . ' />';
            $img .= '<figcaption>' . $this->protectAttr($data[4]) . '</figcaption>';

            $attr = $current_attr . $align_attr;

            return $img;
        }
    }

    private function __parseQ(string $str, string &$attr): string
    {
        $str  = $this->__inlineWalk($str);
        $data = $this->__splitTagsAttr($str);

        $content = $data[0];
        $lang    = (!empty($data[1])) ? $this->protectAttr($data[1], true) : '';

        $attr .= (!empty($lang)) ? ' lang="' . $lang . '"' : '';
        $attr .= (!empty($data[2])) ? ' cite="' . $this->protectAttr($this->protectUrls($data[2])) . '"' : '';

        return $content;
    }

    private function __parseI(string $str, string &$attr): string
    {
        $str  = $this->__inlineWalk($str);
        $data = $this->__splitTagsAttr($str);

        $content = $data[0];
        $lang    = (!empty($data[1])) ? $this->protectAttr($data[1], true) : '';

        $attr .= (!empty($lang)) ? ' lang="' . $lang . '"' : '';

        return $content;
    }

    private function __parseAnchor(string $str, string &$attr)
    {
        $name = $this->protectAttr($str, true);

        if ($name != '') {
            $attr .= ' id="' . $name . '"';
        }
    }

    private function __parseNote(string $str): string
    {
        $i                     = count($this->foot_notes) + 1;
        $id                    = $this->getOpt('note_prefix') . '-' . $i;
        $this->foot_notes[$id] = $this->__inlineWalk($str);

        return '<sup>\[<a href="#' . $id . '" id="rev-' . $id . '">' . $i . '</a>\]</sup>';
    }

    private function __parseInlineHTML(string $str): string
    {
        return str_replace(['&gt;', '&lt;'], ['>', '<'], $str);
    }

    # Obtenir un acronyme
    private function __parseAcronym(string $str, string &$attr): string
    {
        $data = $this->__splitTagsAttr($str);

        $acronym = $data[0];
        $title   = $lang   = '';

        if (count($data) > 1) {
            $title = $data[1];
            $lang  = (!empty($data[2])) ? $this->protectAttr($data[2], true) : '';
        }

        if ($title == '' && !empty($this->acro_table[$acronym])) {
            $title = $this->acro_table[$acronym];
        }

        $attr .= ($title) ? ' title="' . $this->protectAttr($title) . '"' : '';
        $attr .= ($lang) ? ' lang="' . $lang . '"' : '';

        return $acronym;
    }

    # Définition des acronymes, dans le fichier acronyms.txt
    private function __getAcronyms(): array
    {
        $file = $this->getOpt('acronyms_file');
        $res  = [];

        if (file_exists($file)) {
            if (($fc = @file($file)) !== false) {
                foreach ($fc as $v) {
                    $v = trim((string) $v);
                    if ($v != '') {
                        $p = strpos($v, ':');
                        $K = (string) trim(substr($v, 0, $p));
                        $V = (string) trim(substr($v, ($p + 1)));

                        if ($K) {
                            $res[$K] = $V;
                        }
                    }
                }
            }
        }

        return $res;
    }

    # Mots wiki (pour héritage)
    private function parseWikiWord(string $str, &$tag, &$attr, &$type): string
    {
        $tag = '';
//        $attr = '';

        if (isset($this->functions['wikiword'])) {
            return call_user_func($this->functions['wikiword'], $str);
        }

        return $str;
    }

    /* Protection des attributs */
    private function protectAttr(string $str, bool $name = false): string
    {
        if ($name && !preg_match('/^[A-Za-z][A-Za-z0-9_:.-]*$/', $str)) {
            return '';
        }

        return str_replace(["'", '"'], ['&#039;', '&quot;'], $str);
    }

    /* Protection des urls */
    private function protectUrls(string $str): string
    {
        if (preg_match('/^javascript:/', $str)) {
            $str = '#';
        }

        return $str;
    }

    /* Auto BR */
    private function __autoBR(array $m): string
    {
        return $m[1] . str_replace("\n", "<br />\n", $m[2]) . $m[3];
    }

    /* Macro
    --------------------------------------------------- */
    private function __getMacro($s): string
    {
        $s              = is_array($s) ? $s[1] : $s;
        $this->macros[] = str_replace('\"', '"', $s);

        return 'øøø##########MACRO#' . (count($this->macros) - 1) . '#';
    }

    private function __putMacro($id): string
    {
        $id = is_array($id) ? (int) $id[1] : (int) $id;
        if (isset($this->macros[$id])) {
            $content = str_replace("\r", '', $this->macros[$id]);

            $c = explode("\n", $content);

            # première ligne, premier mot
            $fl = trim((string) $c[0]);
            $fw = $fl;

            if ($fl) {
                if (strpos($fl, ' ') !== false) {
                    $fw = substr($fl, 0, strpos($fl, ' '));
                }
                $content = implode("\n", array_slice($c, 1));
            }

            if ($c[0] == "\n") {
                $content = implode("\n", array_slice($c, 1));
            }

            if ($fw) {
                if (isset($this->functions['macro:' . $fw])) {
                    return call_user_func($this->functions['macro:' . $fw], $content, $fl);
                }
            }

            # Si on n'a rien pu faire, on retourne le tout sous
            # forme de <pre>
            return '<pre>' . htmlspecialchars($this->macros[$id]) . '</pre>';
        }

        return '';
    }

    private function __macroHTML($s): string
    {
        return $s;
    }

    /* Aide et debug
    --------------------------------------------------- */
    public function help(): string
    {
        $help['b'] = [];
        $help['i'] = [];

        $help['b'][] = 'Laisser une ligne vide entre chaque bloc <em>de même nature</em>.';
        $help['b'][] = '<strong>Paragraphe</strong> : du texte et une ligne vide';

        if ($this->getOpt('active_title')) {
            $help['b'][] = '<strong>Titre</strong> : <code>!!!</code>, <code>!!</code>, ' .
                '<code>!</code> pour des titres plus ou moins importants';
        }

        if ($this->getOpt('active_hr')) {
            $help['b'][] = '<strong>Trait horizontal</strong> : <code>----</code>';
        }

        if ($this->getOpt('active_lists')) {
            $help['b'][] = '<strong>Liste</strong> : ligne débutant par <code>*</code> ou ' .
                '<code>#</code>. Il est possible de mélanger les listes ' .
                '(<code>*#*</code>) pour faire des listes de plusieurs niveaux. ' .
                'Respecter le style de chaque niveau';
        }

        if ($this->getOpt('active_defl')) {
            $help['b'][] = '<strong>Liste de définitions</strong> : terme(s) débutant(s) par <code>=</code>, ' .
                'définition(s) débutant(s) par <code>:</code>.';
        }

        if ($this->getOpt('active_pre')) {
            $help['b'][] = '<strong>Texte préformaté</strong> : espace devant chaque ligne de texte';
        }

        if ($this->getOpt('active_quote')) {
            $help['b'][] = '<strong>Bloc de citation</strong> : <code>&gt;</code> ou ' .
                '<code>;:</code> devant chaque ligne de texte';
        }

        if ($this->getOpt('active_aside')) {
            $help['b'][] = '<aside>Note de côté</aside> : <code>)</code> devant chaque ligne de texte';
        }

        if ($this->getOpt('active_details')) {
            $help['b'][] = '<details><summary>Sommaire</summary> ... </details> : <code>|</code> en première ligne avec le texte du sommaire, <code>|</code> en derniere ligne du bloc';
        }

        if ($this->getOpt('active_fr_syntax')) {
            $help['i'][] = 'La correction de ponctuation est active. Un espace ' .
                'insécable remplacera automatiquement tout espace ' .
                'précédant les marques ";","?",":" et "!".';
        }

        if ($this->getOpt('active_em')) {
            $help['i'][] = '<strong>Emphase</strong> : deux apostrophes <code>\'\'texte\'\'</code>';
        }

        if ($this->getOpt('active_strong')) {
            $help['i'][] = '<strong>Forte emphase</strong> : deux soulignés <code>__texte__</code>';
        }

        if ($this->getOpt('active_br')) {
            $help['i'][] = '<strong>Retour forcé à la ligne</strong> : <code>%%%</code>';
        }

        if ($this->getOpt('active_ins')) {
            $help['i'][] = '<strong>Insertion</strong> : deux plus <code>++texte++</code>';
        }

        if ($this->getOpt('active_del')) {
            $help['i'][] = '<strong>Suppression</strong> : deux moins <code>--texte--</code>';
        }

        if ($this->getOpt('active_mark')) {
            $help['i'][] = '<mark>Texte marqué</mark> : deux guillemets <code>""texte""</code>';
        }

        if ($this->getOpt('active_sup')) {
            $help['i'][] = '<sup>Exposant</sup> : un accent circonflexe <code>^texte^</code>';
        }

        if ($this->getOpt('active_sub')) {
            $help['i'][] = '<sub>Indice</sub> : un souligné <code>,,texte,,</code>';
        }

        if ($this->getOpt('active_urls')) {
            $help['i'][] = '<strong>Lien</strong> : <code>[url]</code>, <code>[nom|url]</code>, ' .
                '<code>[nom|url|langue]</code> ou <code>[nom|url|langue|titre]</code>.';

            $help['i'][] = '<strong>Image</strong> : comme un lien mais avec une extension d\'image.' .
                '<br />Pour désactiver la reconnaissance d\'image mettez 0 dans un dernier ' .
                'argument. Par exemple <code>[image|image.gif||0]</code> fera un lien vers l\'image au ' .
                'lieu de l\'afficher.' .
                '<br />Il est conseillé d\'utiliser la nouvelle syntaxe.';
        }

        if ($this->getOpt('active_img')) {
            $help['i'][] = '<strong>Image</strong> (nouvelle syntaxe) : ' .
                '<code>((url|texte alternatif))</code>, ' .
                '<code>((url|texte alternatif|position))</code> ou ' .
                '<code>((url|texte alternatif|position|description longue))</code>. ' .
                '<br />La position peut prendre les valeur L ou G (gauche), R ou D (droite) ou C (centré).';
        }

        if ($this->getOpt('active_anchor')) {
            $help['i'][] = '<strong>Ancre</strong> : <code>~ancre~</code>';
        }

        if ($this->getOpt('active_acronym')) {
            $help['i'][] = '<strong>Acronyme</strong> : <code>??acronyme??</code> ou ' .
                '<code>??acronyme|titre??</code>';
        }

        if ($this->getOpt('active_q')) {
            $help['i'][] = '<strong>Citation</strong> : <code>{{citation}}</code>, ' .
                '<code>{{citation|langue}}</code> ou <code>{{citation|langue|url}}</code>';
        }

        if ($this->getOpt('active_i')) {
            $help['i'][] = '<strong>texte différencié</strong> : <code>££texte différencié££</code>, ' .
                '<code>££texte différencié|langue££</code>';
        }

        if ($this->getOpt('active_code')) {
            $help['i'][] = '<strong>Code</strong> : <code>@@code ici@@</code>';
        }

        if ($this->getOpt('active_footnotes')) {
            $help['i'][] = '<strong>Note de bas de page</strong> : <code>$$Corps de la note$$</code>';
        }

        $res = '<dl class="wikiHelp">';

        $res .= '<dt>Blocs</dt><dd>';
        $res .= '<ul><li>';
        $res .= implode('&nbsp;;</li><li>', $help['b']);
        $res .= '.</li></ul>';
        $res .= '</dd>';

        $res .= '<dt>Éléments en ligne</dt><dd>';
        if (count($help['i']) > 0) {
            $res .= '<ul><li>';
            $res .= implode('&nbsp;;</li><li>', $help['i']);
            $res .= '.</li></ul>';
        }
        $res .= '</dd>';

        $res .= '</dl>';

        return $res;
    }
}
