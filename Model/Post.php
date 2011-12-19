<?php

App::uses('Folder', 'Utility');

class Post {

    public $ext = 'md';

    public function findAll($limit = true, $checkArchive = true) {
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
        if ($checkArchive) {
            if ($limit === true || count($posts) < $limit) {
                $this->addArchives($posts, $contents[0], $limit);
            }
        }

        foreach($posts as &$post) {
            $post = $this->metaData($post);
        }
        return $posts;
    }

    public function metaData($file) {
        $year = substr($file, 0, 4);
        $month = substr($file, 5, 2);
        $day = substr($file, 8, 2);
        $slug = substr($file, 11, -3);
        $date = mktime(0, 0, 0, $month, $day, $year);

        return array(
            'file' => $file,
            'title' => str_replace(array('-', '/'), array(' ', ' '), $slug),
            'year' => $year,
            'month' => $month,
            'day' => $day,
            'date' => $date,
            'slug' => $slug,
            'url' => "/$year/$month/$day/$slug.html"
        );
    }

    protected function addArchives(&$posts, $years, $limit) {
        while(($limit === true || count($posts) < $limit) && $years) {
            $year = array_pop($years);
            if (!is_numeric($year) || !strlen($year) === 4) {
                continue;
            }
            $monthfolder = new Folder(Configure::read('PhasePosts') . $year);
            $months = $monthfolder->read();
            $months[0] = array_reverse($months[0]);
            foreach($months[0] as $month) {
                if (!is_numeric($month) || !strlen($month) === 2) {
                    continue;
                }

                $dayfolder = new Folder(Configure::read('PhasePosts') . $year . DS . $month);
                $days = $dayfolder->read();
                $days[0] = array_reverse($days[0]);
                foreach($days[0] as $day) {
                    if (!is_numeric($day) || !strlen($day) === 2) {
                        continue;
                    }

                    $postfolder = new Folder(Configure::read('PhasePosts') . $year . DS . $month . DS . $day);
                    $files = $postfolder->read();
                    foreach($files[1] as $file) {
                        if ($limit === true || count($posts) < $limit) {
                            $posts[] = "$year/$month/$day/$file";
                        }
                    }
                }
            }
        }
    }
}
