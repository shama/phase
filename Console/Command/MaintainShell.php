<?php

App::uses('AppShell', 'Console/Command');

/**
 * PhaseShell
 */
class MaintainShell extends AppShell {

    public $tasks = array(
        'Archive'
    );

    /**
     * getOptionParser
     */
    public function getOptionParser() {
        $parser = new ConsoleOptionParser($this->name);
        $parser->description(array(
            __d('phase', 'Maintenance tasks for your phase install')
        ))->addSubcommand('archive', array(
            'help' => __d('phase', 'Move older posts into subfolders.'),
            'parser' => $this->Archive->getOptionParser()
        ));

        return $parser;
    }
}
