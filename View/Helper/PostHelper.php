<?php

App::uses('AppHelper', 'View/Helper');

class PostHelper extends AppHelper {

    public $helpers = array(
        'Markdown',
        'YFM',
        'Text'
    );

    public function data($post, $type = 'Posts') {
        if ($type === 'Posts') {
            $contents = file_get_contents(Configure::read('PhasePosts') . $post['file']);
        } else  {
            $contents = file_get_contents(Configure::read('PhaseViews') . $type . DS . $post['file']);
        }
        $parsed = $this->YFM->parse($contents);
        $post += $parsed;
        if (empty($post['intro'])) {
            $post['intro'] = substr($post['contents'], 0, strpos($post['contents'], "\n", 1));
            if (strlen($post['intro']) < 200) {
                $post['intro'] = $this->Text->truncate($post['contents'], 400);
            }
            $post['intro'] = '<p>' . $post['intro'] . '</p>';
        }
        $post['contents'] = $this->Markdown->process($post['contents']);

        return $post;
    }
}
