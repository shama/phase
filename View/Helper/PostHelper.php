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

        $post['contents'] = $this->Markdown->process($post['contents']);

        if (empty($post['intro'])) {
            $plainText = strip_tags($post['contents']);
            $post['intro'] = substr($plainText, 0, strpos($plainText, "\n", 1));
            if (strlen($post['intro']) < 200) {
                $post['intro'] = $this->Text->truncate($plainText, 400);
            }
            $post['intro'] = '<p>' . $post['intro'] . '</p>';
        }

        return $post;
    }
}
