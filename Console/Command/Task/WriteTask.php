<?php

App::uses('AppShell', 'Console/Command');
App::uses('Folder', 'Utility');

/**
 * WriteTask
 */
class WriteTask extends AppShell {

    /**
     * Create a new draft post at todays date
     */
    public function execute() {
        $slug = date("Y-m-d-") . 'draft-post.md';
    }

	public function getOptionParser() {
		$parser = parent::getOptionParser();
        $parser->description(array(
            __d('phase', 'Crete a new draft post at today\'s date'),
        ))->addArgument('title', array(
            'help' => __d('phase', 'The title for the post'),
            'required' => false,
        ));
        return $parser;
	}
}
