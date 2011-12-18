<?php

App::uses('AppShell', 'Console/Command');

/**
 * BuildShell
 *
 * Writes a static version of your site for deployment
 */
class BuildShell extends AppShell {

    public $tasks = array(
        'Build',
        'Deploy'
    );

    /**
     * getOptionParser
     */
    public function getOptionParser() {
        $parser = new ConsoleOptionParser($this->name);
        return $parser;
    }

    public function main() {
    }
}
