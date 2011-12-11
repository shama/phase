<?php

App::uses('AppHelper', 'View/Helper');

class YFMHelper extends AppHelper {

    public $settings = array();

    protected $defaultSettings = array(
        'run' => 'afterRender'
    );

	public function __construct(View $View, $settings = array()) {
        $this->settings = $this->defaultSettings + $settings;
        return parent::__construct($View, $settings);
    }

    public function afterRender($filename) {
        if ($this->settings['run'] !== 'afterRender') {
            return;
        }

        $parsed = $this->process($this->_View->output);
        if ($parsed && array_key_exists('YFMParseRemainder', $parsed)) {
            $this->_View->output = $parsed['YFMParseRemainder'];
        }
    }

    public function process($input = '') {
        $parsed = $this->parse($input);
        if (!empty($parsed['meta_title'])) {
            $parsed['title_for_layout'] = $parsed['meta_title'];
            unset($parsed['meta_title']);
        }
        if(!empty($parsed['layout'])) {
            $this->_View->layout = $parsed['layout'];
        }
        $this->_View->set($parsed);
        return $parsed;
    }

    public function parse($input = '') {
        if (strpos($input, '---') !== 0) {
            return;
        }

        preg_match('@^---(.*)\n---\n@ms', $input, $match);
        if (!$match) {
            return;
        }

        if (!$this->yaml) {
            App::import('Vendor', 'sfYamlParser', array('file' => 'yaml/lib/sfYamlParser.php'));
            $this->yaml = new sfYamlParser();
        }

        $return = array();
        try {
            $return = $this->yaml->parse($match[1]);
        } catch (InvalidArgumentException $e) {
            $return['YFMParseError'] = 'Unable to parse the YAML string: ' . $e->getMessage();
        }


        $prevKey = null;
        foreach($return as $key => $value) {
            if (is_int($key)) {
                if ($prevKey) {
                    $return[$prevKey][] = $value;
                    unset($return[$key]);
                }
            } elseif (is_null($value)) {
                $return[$key] = array();
                $prevKey = $key;
            }
        }
        $return['YFMParseRemainder'] = substr($input, strlen($match[0]));

        return $return;
    }
}
