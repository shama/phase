<?php

App::uses('AppController', 'Controller');

class PostsController extends AppController {

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
 * User the application view class
 *
 * @var string
 */
    public $viewClass = 'Phase';

    public function index() {
        $extLength = strlen($this->ext);

        App::uses('Folder', 'Utility');
        $folder = new Folder('../Contents');
        $contents = $folder->read();
        foreach($contents[1] as $i => $file) {
            if ($file[0] === '.' || substr($file, - $extLength) !== $this->ext) {
                unset($contents[1][$i]);
            }
        }

        $this->set('posts', array_reverse($contents[1]));
    }

/**
 * View a single post
 *
 * @param mixed What page to display
 */
	public function view($first = '') {
		if (!$first) {
			$this->redirect('/');
		}

        $this->viewPath = '../Contents';

		$path = func_get_args();
		$count = count($path);
        $last = end($path);
        if (substr($last, -5) === '.html') {
            $path[count($path) -1] = substr($last, 0, -5);
        }

		$this->render(implode('-', $path));
	}
}
