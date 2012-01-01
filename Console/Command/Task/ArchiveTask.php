<?php

App::uses('AppShell', 'Console/Command');

class ArchiveTask extends AppShell {

    /**
     * Keep the root of your posts dir clear by moving older posts into subfolders
     *
     * This has no effect on the public site, it only aids you as the author in keeping your
     * visible recent posts list manageably small
     */
    public function execute() {
        $root = Configure::read('PhasePosts');

        $post = ClassRegistry::init('Post');
        $posts = $post->findAll(true, false);

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
