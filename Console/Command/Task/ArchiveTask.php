<?php

App::uses('AppShell', 'Console/Command');

class ArchiveTask extends AppShell {

    /**
     * Create a new draft post at todays date
     */
    public function execute() {
        $root = Configure::read('PhasePosts');

        $post = ClassRegistry::init('Post');
        $posts = $post->findAll();

        $yes = false;
        foreach($posts as $post) {
            extract($post);
            $archiveLocation = "$year/$month/$day/$slug.md";

            $source = $root . $post['file'];
            $yorn = $yes;
            if (!$yorn) {
                $yorn = $this->in(__d('phase', "Move\t/%s\nto\t/%s?\ny/n/a/q", $post['file'], $archiveLocation));
                if ($yorn === 'q') {
                    return $this->_stop();
                }
                if ($yorn === 'a') {
                    $yes = $yorn = 'y';
                }
            }
            if ($yorn === 'y') {
                $target = $root . $archiveLocation;
                $targetDir = dirname($target);
                if(!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }

                $sourceEsc = escapeshellarg($source);
                $targetEsc = escapeshellarg($target);
                if (file_exists($target)) {
                    unlink($target);
                }

                `cd $root && git mv $sourceEsc $targetEsc`;
                if (!file_exists($target)) {
                    rename($source, $target);
                }

                if (file_exists($target)) {
                    $this->out("Post moved to $archiveLocation");
                }
            }
        }
    }

    /**
     * getOptionParser
     */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
        $parser->description(array(
            __d('phase', 'Move posts into dated subfolders'),
        ));
        return $parser;
	}
}
