<?php

App::uses('AppHelper', 'View/Helper');

class MarkdownHelper extends AppHelper {

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

        if (strpos($this->_View->output, '---') === 0) {
            preg_match('@^---(.*)\n---\n@ms', $this->_View->output, $match);
            if ($match) {
                $this->_View->output = substr($this->_View->output, strlen($match[0]));
                $this->parseYamlFrontMatter($match[1]);
            }
        }
        $this->_View->output = $this->process($this->_View->output);
    }

    public function process($input = '') {
        if (!function_exists('Markdown')) {
            App::import('Vendor', 'Markdown/Markdown');
        }
        return Markdown($input);
    }

    protected function parseYamlFrontMatter($string) {
        if (!$this->yaml) {
            App::import('Vendor', 'sfYamlParser', array('file' => 'yaml/lib/sfYamlParser.php'));
            $this->yaml = new sfYamlParser();
        }

        try {
            $values = $this->yaml->parse($string);
        } catch (InvalidArgumentException $e) {
            echo "Unable to parse the YAML string: " . $e->getMessage();
        }
    }

}
