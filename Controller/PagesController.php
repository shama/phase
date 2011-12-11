<?php

App::uses('AppController', 'Controller');

class PagesController extends AppController {

/**
 * Look for markdown files
 *
 * @var string
 */
    public $ext = '.md';

    public $helpers = array(
        'Html',
        'YFM',
        'Markdown'
    );

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

/**
 * viewPath
 *
 * @var string
 */
	public $viewPath = '../Contents';

/**
 * User the application view class
 *
 * @var string
 */
    public $viewClass = 'Phase';

/**
 * Displays a view
 *
 * @param mixed What page to display
 */
	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}

        $last = end($path);
        if (substr($last, -5) === '.html') {
            $path[count($path) -1] = substr($last, 0, -5);
        }

		$this->render(implode('-', $path));
	}
}
