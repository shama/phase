<?php

App::uses('Controller', 'Controller');

class AppController extends Controller {

    public $components = array(
        'Session'
    );

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
        'Post',
        'Session'
    );

    public function beforeRender() {
        $meta_description = $meta_keywords = $meta_author = '';
		$this->set(compact('meta_description', 'meta_keywords', 'meta_author'));
    }
}
