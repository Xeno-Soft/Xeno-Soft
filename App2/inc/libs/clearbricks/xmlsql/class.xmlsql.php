<?php
/**
 * @class xmlsql
 *
 * @package Clearbricks
 *
 * @copyright Olivier Meunier & Association Dotclear
 * @copyright GPL-2.0-only
 */
class xmlsql
{
    protected $con;
    protected $xml;

    protected $tables;

    protected $stack;

    public $test_version = 0;

    public function __construct(&$con, $xml)
    {
        $this->con = &$con;
        $this->xml = $xml;

        $schema       = dbSchema::init($this->con);
        $this->tables = $schema->getTables();
        unset($schema);
    }

    public function replace($str, $rep)
    {
        $this->xml = str_replace($str, $rep, $this->xml);
    }

    public function execute($version = 0)
    {
        $this->test_version = $version;

        $x = @simplexml_load_string($this->xml);
        if (!$x) {
            throw new Exception('Unable to load XML file.');
        }

        $this->parseNode($x);
    }

    protected function parseNode($node)
    {
        foreach ($node->children() as $n) {
            switch (dom_import_simplexml($n)->nodeName) {
                case 'test':
                    $this->performTest($n);

                    break;
                case 'action':
                    $this->performAction($n);

                    break;
            }
        }
    }

    protected function performTest($node)
    {
        $test = [];

        /* Test like:
        <test type="table" name="table name" [eq="neq"]>...</test>
         */
        if (isset($node['type']) && (string) $node['type'] == 'table') {
            $test['result'] = in_array($node['name'], $this->tables);
            $test['label']  = 'Table %s does not exists';
            $test['string'] = (string) $node['name'];

            $xtest = $node;
        }
        /* Test syntax:
        <test type="column" name"table.column" [eq="neq"]>...</test>
         */
        elseif (isset($node['type']) && (string) $node['type'] == 'column') {
            $c = explode('.', (string) $node['name']);

            if (count($c) != 2) {
                return false;
            }

            [$table, $col] = $c;

            $rs = $this->con->getColumns($table);

            $test['result'] = isset($rs[$col]);
            $test['label']  = 'Column %s does not exists';
            $test['string'] = (string) $node['name'];

            $xtest = $node;
        }
        /* Test syntax:
        <test type="version" name="version number" [comp=">"]>...</test>
         */
        elseif (isset($node['type']) && (string) $node['type'] == 'version') {
            $comp           = $node['comp'] ?? '>';
            $test['result'] = version_compare($node['name'], $this->test_version, $comp);
            $test['label']  = 'Version %s is too low';
            $test['string'] = (string) $node['name'];

            $xtest = $node;
        }

        # End tests
        if (isset($xtest)) {
            if ($xtest['eq'] == 'neq') {
                $test['result'] = !$test['result'];
            }

            if (isset($xtest['alert'])) {
                $test['alert'] = (bool) (int) $xtest['alert'];
            } else {
                $test['alert'] = false;
            }

            if (isset($xtest['label'])) {
                $test['label'] = (string) $xtest['label'];
            }
            if (isset($xtest['string'])) {
                $test['string'] = (string) $xtest['string'];
            }
            unset($xtest);

            # Test false
            if (!$test['result']) {
                if ($test['alert']) {
                    throw new Exception(sprintf($test['label'], $test['string']));
                }
            }
            # Test true
            else {
                $this->parseNode($node);
            }
        } else {
            return false;
        }
    }

    protected function performAction($node)
    {
        $req = trim((string) $node);
        if ($req) {
            try {
                $this->con->execute($req);
            } catch (Exception $e) {
                if ($node['silent'] != 1) {
                    throw $e;
                }
            }
        }
    }
}
