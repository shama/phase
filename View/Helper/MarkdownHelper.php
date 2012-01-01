<?php

App::uses('AppHelper', 'View/Helper');
App::import('Vendor', 'Markdown/Markdown');

class MarkdownHelper extends AppHelper {

    /**
     * settings
     */
    public $settings = array();

    /**
     * parser
     */
    protected $parser;

    /**
     * defaultSettings
     */
    protected $defaultSettings = array(
        'run' => 'afterRender'
    );

    /**
     * __construct
     *
     * @param View $View
     * @param array $settings
     */
	public function __construct(View $View, $settings = array()) {
        $this->settings = $this->defaultSettings + $settings;
        $this->parser = new PhaseMarkdownParser();
        if (!empty($View->request->params['ext'])) {
            $ext = $View->request->params['ext'];
            if ($ext !== 'html') {
                $this->settings['run'] = 'never';
            }
        }
        return parent::__construct($View, $settings);
    }

    /**
     * afterRender
     *
     * @param mixed $filename
     */
    public function afterRender($filename) {
        if ($this->settings['run'] !== 'afterRender') {
            return;
        }

        $this->_View->output = $this->process($this->_View->output);
    }

    /**
     * process
     *
     * @param string $input
     */
    public function process($input = '') {
        return $this->parser->transform($input);
    }
}

/**
 * PhaseMarkdownParser
 *
 * Overridden to inject automatic header anchor links
 */
class PhaseMarkdownParser extends MarkdownExtra_Parser {

    /**
     * headerIds
     *
     * Stack of ids already used in header links
     */
    protected $headerIds = array();

    function _doHeaders_callback_atx($matches) {
        if (empty($matches[3])) {
            $matches[3] = Inflector::slug($matches[2], '-');
        }
        $link = $this->getAnchorLink($matches[3]);

		$level = strlen($matches[1]);
		$attr  = $this->_doHeaders_attr($id =& $matches[3]);
		$block = "<h$level$attr>".$this->runSpanGamut($matches[2])."$link</h$level>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}

    /**
     * _doHeaders_callback_setext
     *
     * @param mixed $matches
     */
	function _doHeaders_callback_setext($matches) {
        if (!$matches[2]) {
            $matches[2] = Inflector::slug($matches[1], '-');
        }
        $link = $this->getAnchorLink($matches[2]);

		if ($matches[3] == '-' && preg_match('{^- }', $matches[1]))
			return $matches[0];
		$level = $matches[3]{0} == '=' ? 1 : 2;
		$attr  = $this->_doHeaders_attr($id =& $matches[2]);
		$block = "<h$level$attr>".$this->runSpanGamut($matches[1])."$link</h$level>";
		return "\n" . $this->hashBlock($block) . "\n\n";
	}

    /**
     * getAnchorLink
     *
     * Generate a unique id, and return a link pointing at it
     *
     * @param mixed $id
     */
    protected function getAnchorLink(&$id) {
        $i = 0;
        $suffix = '';
        while (in_array($id . $suffix, $this->headerIds)) {
            $suffix = '-' . ++$i;
        }
        $id .= $suffix;
        $this->headerIds[] = $id;

        return '<a class="headerAnchor" href="#' . $id . '">§</a>';
    }
}
