<?php

App::uses('AppHelper', 'View/Helper');

class PostHelper extends AppHelper {

    public $helpers = array(
        'Markdown',
        'YFM',
        'Text'
    );

    public function metaData($file) {
        $year = substr($file, 0, 4);
        $month = substr($file, 5, 2);
        $day = substr($file, 8, 2);
        $slug = substr($file, 11, -3);
        $date = mktime(0, 0, 0, $month, $day, $year);

        return array(
            'file' => $file,
            'title' => str_replace('-', ' ', $slug),
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'date' => $date,
            'slug' => $slug,
            'url' => "/$year/$month/$day/$slug.html"
        );
    }

    public function data($file, $type = 'Posts') {
        if (!$file) {
            return;
        }
        $return = $this->metaData($file);
        if ($type === 'Posts') {
            $contents = file_get_contents(Configure::read('PhasePosts') . $file);
        } else  {
            $contents = file_get_contents(Configure::read('PhaseViews') . $type . DS . $file);
        }
        $parsed = $this->YFM->parse($contents);
        $return += $parsed;
        if (empty($return['intro'])) {
            $return['intro'] = substr($return['contents'], 0, strpos($return['contents'], "\n", 1));
            if (strlen($return['intro']) < 200) {
                $return['intro'] = $this->Text->truncate($return['contents'], 400);
            }
            $return['intro'] = '<p>' . $return['intro'] . '</p>';
        }
        $return['contents'] = $this->Markdown->process($return['contents']);

        return $return;
    }
}
