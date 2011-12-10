<?php

App::uses('AppHelper', 'View/Helper');

class MarkdownHelper extends AppHelper {

    public $settings = array();

    protected $defaultSettings = array(
        'run' => 'afterLayout'
    );

	public function __construct(View $View, $settings = array()) {
        $this->settings = $this->defaultSettings + $settings;
        return parent::__construct($View, $settings);
    }

    public function beforeLayout($layoutFile) {
        if ($this->settings['run'] !== 'beforeLayout') {
            return;
        }
        $this->output = $this->process($this->output);
    }

    public function afterLayout($layoutFileName) {
        if ($this->settings['run'] !== 'afterLayout') {
            return;
        }
        $this->output = $this->process($this->output);
    }

    public function process($input = '') {
        if (!function_exists('Markdown')) {
            App::import('Vendor', 'Markdown/Markdown');
        }
        return Markdown($input);
    }

}
