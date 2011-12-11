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

        $this->_View->output = $this->process($this->_View->output);
    }

    public function process($input = '') {
        if (!function_exists('Markdown')) {
            App::import('Vendor', 'Markdown/Markdown');
        }
        return Markdown($input);
    }
}
