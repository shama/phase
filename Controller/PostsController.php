<?php

App::uses('AppController', 'Controller');

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

    public function archives() {
        $posts = $this->Post->findAll($this->ext, true);
        $title_for_layout = 'All posts';

        if ($this->params->params['ext'] === 'xml') {
            $this->viewPath = 'Posts/xml';
        }

        $this->set(compact('posts', 'title_for_layout'));
    }

    public function home() {
        $posts = $this->Post->findAll($this->ext, 6);
        $title_for_layout = 'Recent writing';

        $latest = array_shift($posts);
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
        if (file_exists(Configure::read('PhasePosts') . "$year/$month/$day/$slug.md")) {
		    return $this->render("$year/$month/$day/$slug");
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

		$this->render(implode('-', $path));
	}
}
