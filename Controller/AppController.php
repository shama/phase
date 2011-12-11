<?php

App::uses('Controller', 'Controller');

class AppController extends Controller {

    function beforeRender() {
        $meta_description = $meta_keywords = $meta_author = '';
		$this->set(compact('meta_description', 'meta_keywords', 'meta_author'));
    }
}
