<?php

App::uses('AppShell', 'Console/Command');
App::uses('Folder', 'Utility');

/**
 * BuildTask
 */
class DeployTask extends AppShell {

    /**
     * Wipe the output diretory and repopulate it using what you can see
     * browsing your development install as the input
     */
    public function execute() {
        $date = date("Y-m-d-Hi");
        $this->out('Uploading new version');

        $dryRun = false;
        $output = rtrim($this->outputDir, '/');
        $server = Configure::read('Phase.deploy.server');
        $source = Configure::read('Phase.deploy.source');
        $webroot = Configure::read('Phase.deploy.public');
        $domain = 'ad7six.com';

        $commands = array(
            "rsync -rv $output/ $server:$source{$date}",
            "ssh $server 'rm $webroot && ln -sf $source{$date} $webroot'"
        );

        foreach($commands as $command) {
            $this->out($command);
            if (!$dryRun) {
                passthru($command);
            }
        }
    }

	public function getOptionParser() {
		$parser = parent::getOptionParser();
        $parser->description(array(
            __d('phase', 'Generate a static version of the site for deployment'),
        ))->addArgument('output', array(
            'help' => __d('phase', 'Where to put the generated files'),
            'required' => false,
        ));
        return $parser;

		return $parser->description(
				__d('cake_console', 'Bake a controller for a model. Using options you can bake public, admin or both.')
			)->addArgument('name', array(
				'help' => __d('cake_console', 'Name of the controller to bake. Can use Plugin.name to bake controllers into plugins.')
			))->addOption('public', array(
				'help' => __d('cake_console', 'Bake a controller with basic crud actions (index, view, add, edit, delete).'),
				'boolean' => true
			))->addOption('admin', array(
				'help' => __d('cake_console', 'Bake a controller with crud actions for one of the Routing.prefixes.'),
				'boolean' => true
			))->addOption('plugin', array(
				'short' => 'p',
				'help' => __d('cake_console', 'Plugin to bake the controller into.')
			))->addOption('connection', array(
				'short' => 'c',
				'help' => __d('cake_console', 'The connection the controller\'s model is on.')
			))->addSubcommand('all', array(
				'help' => __d('cake_console', 'Bake all controllers with CRUD methods.')
			))->epilog(__d('cake_console', 'Omitting all arguments and options will enter into an interactive mode.'));
	}
}
