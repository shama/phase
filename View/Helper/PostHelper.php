<?php

App::uses('AppHelper', 'View/Helper');

class PostHelper extends AppHelper {

    public $helpers = array(
        'YFM',
        'Text'
    );

    public function metaData($file) {
        $date = mktime(
            0,
            0,
            0,
            substr($file, 5, 2),
            substr($file, 8, 2),
            substr($file, 0, 4)
        );
        return array(
            'url' => '/' . substr($file, 0, -3),
            'title' => str_replace('-', ' ', substr($file, 11, -3)),
            'date' => $date
        );
    }

    public function data($file) {
        $return = $this->metaData($file);
        $contents = file_get_contents(APP . 'View' . DS . Configure::read('ContentsFolder') . DS . $file);
        $parsed = $this->YFM->parse($contents);
        $return += $parsed;
        if (empty($return['intro'])) {
            $return['intro'] = substr($return['YFMParseRemainder'], 0, strpos($return['YFMParseRemainder'], "\n", 1));
            if (strlen($return['intro']) < 200) {
                $return['intro'] = $this->Text->truncate($return['YFMParseRemainder'], 400);
            }
            $return['intro'] = '<p>' . $return['intro'] . '</p>';
        }

        return $return;
    }
}
