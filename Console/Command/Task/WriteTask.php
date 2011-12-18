<?php

App::uses('AppShell', 'Console/Command');

/**
 * WriteTask
 */
class WriteTask extends AppShell {

    /**
     * template for a new post
     */
    protected $template = <<<EOT
---
layout: post
title: %title%
meta_title: %title%
meta_description: %title% - update this
meta_keywords:
- "a term"
tags:
- "a tag"
---

start typing.
EOT;

    /**
     * Create a new draft post at todays date
     */
    public function execute() {
        $title = $this->args[0];
        $filename = date("Y-m-d-") . Inflector::slug($title) . '.md';

        $path = Configure::read('PhasePosts');
        $draft = str_replace('%title%', $title, $this->template);
        file_put_contents($path . $filename, $draft);

        $this->out("$filename created");
    }

    /**
     * getOptionParser
     */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
        $parser->description(array(
            __d('phase', 'Crete a new draft post at today\'s date'),
        ))->addArgument('title', array(
            'help' => __d('phase', 'The title for the post'),
            'required' => true
        ));
        return $parser;
	}
}
