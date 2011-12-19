<?php

App::uses('AppController', 'Controller');

class InstallerController extends AppController {

    function go() {
        App::uses('Folder', 'Utility');

        $source = APP . 'Skel';
        $target = Configure::read('PhaseRoot');
        $folder = new Folder($source);
        $folder->copy($target);

        $this->Session->flash("Contents of $source copied to $target");
        $this->redirect('/phase/about');
    }

    function about() {
    }
}
