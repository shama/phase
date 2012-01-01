<?php

App::uses('AppShell', 'Console/Command');

/**
 * PhaseShell
 */
class PhaseShell extends AppShell {

    public $tasks = array(
        'Write',
        'Build',
        'Deploy'
    );

    /**
     * getOptionParser
     */
    public function getOptionParser() {
        $parser = new ConsoleOptionParser($this->name);
        $parser->description(array(
            __d('phase', 'Manage your phase-built site')
        ))->addSubcommand('write', array(
            'help' => __d('phase', 'Create a new post.'),
            'parser' => $this->Write->getOptionParser()
        ))->addSubcommand('build', array(
            'help' => __d('phase', 'Generate a static version of your application.'),
            'parser' => $this->Build->getOptionParser()
        ))->addSubcommand('deploy', array(
            'help' => __d('phase', 'Copy files to public server.'),
            'parser' => $this->Deploy->getOptionParser()
        ));

        return $parser;
    }

    public function go() {
        $this->Build->execute();
        $this->Deploy->execute();
    }
}
