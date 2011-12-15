<?php

App::uses('AppController', 'Controller');
App::uses('Folder', 'Utility');

class PostsController extends AppController {

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
        return parent::__construct($request, $response);
    }

    public function archives() {
        $extLength = strlen($this->ext);

        $folder = new Folder(Configure::read('PhasePosts'));
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

        $folder = new Folder(Configure::read('PhasePosts'));
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
 * @param string $year
 * @param string $month
 * @param string $day
 * @param string $slug
 */
	public function viewDated($year = '', $month = '', $day = '', $slug = '') {
		if (!$year) {
			$this->redirect('/');
		}

        $this->set('postDate', mktime(0, 0, 0, $month, $day, $year));

        if (substr($slug, -5) === '.html') {
            $slug = substr($slug, 0, -5);
        }

		$this->render("$year-$month-$day-$slug");
	}

/**
 * View a something in the posts folder
 *
 * @param mixed What page to display
 */
	public function view() {
		$path = func_get_args();
		if (!$path) {
			$this->redirect('/');
		}

		$count = count($path);
        $last = end($path);
        if (substr($last, -5) === '.html') {
            $path[count($path) -1] = substr($last, 0, -5);
        }

		$this->render(implode('-', $path));
	}
}
