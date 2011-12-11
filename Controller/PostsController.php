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

    public function __construct($request = null, $response = null) {
        $this->viewPath = Configure::read('ContentsFolder');
        return parent::__construct($request, $response);
    }

    public function home() {
        $extLength = strlen($this->ext);

        App::uses('Folder', 'Utility');
        $folder = new Folder($this->viewPath);
        $contents = $folder->read();

        $posts = array();
        foreach($contents[1] as $i => $file) {
            if ($file[0] === '.' || substr($file, - $extLength) !== $this->ext) {
                unset($contents[1][$i]);
                continue;
            }
            $date = mktime(
                0,
                0,
                0,
                substr($file, 5, 2),
                substr($file, 8, 2),
                substr($file, 0, 4)
            );
            $post = array(
                'url' => '/' . substr($file, 0, -3),
                'title' => str_replace('-', ' ', substr($file, 11, -3)),
                'date' => $date
            );
            $posts[] = $post;
        }
        $latest = end($posts);
        $posts = array_slice(array_reverse($posts), 1, 6);

        $this->set(compact('posts', 'latest'));
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
