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
meta_description: %title% description
meta_keywords:
%keywords%
tags:
%tags%
---

start typing.
EOT;

    /**
     * Create a new draft post at todays date
     */
    public function execute() {

        $title = $this->args[0];
        $keywords = '- "a term"';
        $tags = '- "a tag"';

        if (!empty($this->params['tags'])) {
            $tags = explode(',', $this->params['tags']);
            $tags = array_map('trim', $tags);
            $tags = '- "' . implode($tags, "\"\n- \"") . '"';
        }

        $replacements = array(
            '%title%' => $title,
            '%keywords%' => $keywords,
            '%tags%' => $tags,
        );
        $draft = str_replace(array_keys($replacements), array_values($replacements), $this->template);
        $filename = date("Y-m-d-") . Inflector::slug($title, '-') . '.md';
        file_put_contents(Configure::read('PhasePosts') . $filename, $draft);

        $this->out("$filename created");
    }

    /**
     * getOptionParser
     */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
        $parser->description(array(
            __d('phase', 'Crete a new draft post at today\'s date'),
        ))->addOption('tags', array(
            'help' => __d('phase', 'Comma separated list of tags for this post'),
        ))->addArgument('title', array(
            'help' => __d('phase', 'The title for the post'),
            'required' => true
        ));
        return $parser;
	}
}
