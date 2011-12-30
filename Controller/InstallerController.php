<?php

App::uses('AppController', 'Controller');

class InstallerController extends AppController {

    function go() {
        App::uses('Folder', 'Utility');

        $source = APP . 'Skel';
        $target = Configure::read('PhaseRoot');
        $folder = new Folder($source);
        $folder->copy($target);

        $source = APP . 'Skel' . DS . 'webroot';
        $target = WWW_ROOT;
        $folder = new Folder($source);
        $folder->copy($target);

        $contents = "---
layout: post
title: About Phase
---
";
        $contents .= file_get_contents(APP . 'README.md');
        $filename = date('Y-m-d', time() - 60*60*24) . '-about-phase.md';
        file_put_contents(Configure::read('PhasePosts') . $filename, $contents);

        $contents = "---
layout: post
title: First post
---

This is an example post - edit/rename/delete however you want. The readme has also been converted
to a post - it may contain useful syntax examples if you're unfamiliar with markdown.

First steps:

* Open Config/core.php and set the config settings used by Phase.
* Use the phase cli ( $ Console/cake phase write ) to create your first real post.
* Optionally modify the design to your liking
* Start writing

";
        $filename = date('Y-m-d') . '-first-post.md';
        file_put_contents(Configure::read('PhasePosts') . $filename, $contents);

        $this->Session->setFlash("Contents of $source copied to $target");
        $this->redirect('/');
    }

    function about() {
    }
}
