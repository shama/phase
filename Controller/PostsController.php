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
        'Markdown',
        'Post'
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

    public function __construct($request = null, $response = null) {
        $this->viewPath = Configure::read('ContentsFolder');
        return parent::__construct($request, $response);
    }

    public function archives() {
        $extLength = strlen($this->ext);

        App::uses('Folder', 'Utility');
        $folder = new Folder(APP . 'View' . DS . $this->viewPath);
        $contents = $folder->read();

        $posts = array();
        foreach($contents[1] as $i => $file) {
            if ($file[0] === '.' || substr($file, - $extLength) !== $this->ext) {
                continue;
            }
            $posts[] = $file;
        }
        $posts = array_reverse($posts);

        $title_for_layout = '';

        $this->set(compact('posts', 'title_for_layout'));
    }

    public function home() {
        $extLength = strlen($this->ext);

        App::uses('Folder', 'Utility');
        $folder = new Folder(APP . 'View' . DS . $this->viewPath);
        $contents = $folder->read();

        $posts = array();
        foreach($contents[1] as $i => $file) {
            if ($file[0] === '.' || substr($file, - $extLength) !== $this->ext) {
                continue;
            }
            $posts[] = $file;
        }

        $latest = end($posts);
        $posts = array_slice(array_reverse($posts), 1, 6);

        $title_for_layout = '';

        $this->set(compact('posts', 'latest', 'title_for_layout'));
    }

/**
 * View a single post
 *
 * @param mixed What page to display
 */
	public function view($year = '', $month = '', $day = '') {
		if (!$year) {
			$this->redirect('/');
		}

        if (is_numeric($year) && is_numeric($month) && is_numeric($day)) {
            $this->set('postDate', mktime(0, 0, 0, $month, $day, $year));
        }

		$path = func_get_args();
		$count = count($path);
        $last = end($path);
        if (substr($last, -5) === '.html') {
            $path[count($path) -1] = substr($last, 0, -5);
        }

		$this->render(implode('-', $path));
	}
}
