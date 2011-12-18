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

    public function upload() {
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
}
