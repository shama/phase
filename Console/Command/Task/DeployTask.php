<?php

App::uses('AppShell', 'Console/Command');

/**
 * BuildTask
 */
class DeployTask extends AppShell {

    public $outputDir = 'publish';

	public function getOptionParser() {
		$parser = parent::getOptionParser();
        $parser->description(array(
            __d('phase', 'Upload static site to public server'),
        ))->addArgument('path', array(
            'help' => __d('phase', 'Source dir to copy, defaults to "%s"', $this->outputDir),
        ))->addOption('server', array(
            'help' => __d('phase', 'Remote server name'),
        ))->addOption('remotePath', array(
            'help' => __d('phase', 'Location of deployed versions'),
        ))->addOption('docroot', array(
            'help' => __d('phase', 'Remote docroot'),
        ))->addOption('dry-run', array(
            'help' => __d('phase', 'Don\'t do anything, just show the commands'),
            'boolean' => true,
            'short' => 'n'
        ));
        return $parser;
	}

    /**
     * Wipe the output directory and repopulate it using what you can see
     * browsing your development install as the input
     */
    public function execute() {
        $date = date("Y-m-d-Hi");

        $dryRun = false;
        $output = rtrim($this->outputDir, '/');
        $server = Configure::read('Phase.deploy.server');
        $source = Configure::read('Phase.deploy.source');
        $docroot = Configure::read('Phase.deploy.public');

        if (!empty($this->params['dry-run'])) {
            $dryRun = true;
        }
        if (!empty($this->params['server'])) {
            $server = $this->params['server'];
        }
        if (!empty($this->params['remotePath'])) {
            $source = rtrim($this->params['remotePath'], '/') . '/';
        }
        if (!empty($this->params['docroot'])) {
            $docroot = $this->params['docroot'];
        }

        $commands = array(
            "rsync -rv $output/ $server:$source{$date}",
            "ssh $server 'rm $docroot && ln -sf $source{$date} $docroot'"
        );

        if ($dryRun) {
            $this->out('Commands to be run:');
        } else {
            $this->out('Uploading new version');
        }

        foreach($commands as $command) {
            $this->out($command);
            if (!$dryRun) {
                passthru($command);
            }
        }
    }
}
