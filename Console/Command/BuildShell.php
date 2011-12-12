<?php

App::uses('AppShell', 'Console/Command');

class BuildShell extends AppShell {

    protected $outputDir = 'publish';

    protected $fourOFours = array();

    protected $urlStack = array();

    public function getOptionParser() {
        $parser = new ConsoleOptionParser($this->name);
        $parser->description(array(
            __d('phase', 'Generate a static version of the site for deployment'),
        ))->addArgument('output', array(
            'help' => __d('phase', 'Where to put the generated files'),
            'required' => false,
        ));
        return $parser;
    }

    public function main() {
        exec('rm -rf ' . $this->outputDir);

        $this->recurse();

        $this->err("404s!");
        if ($this->fourOFours) {
            foreach($this->fourOFours as $url => $referers) {
                $this->out("\t$url");
            }
        }
    }

    protected function recurse() {
        $this->urlStack[] = '/';
        $this->urlStack[] = '/robots.txt';
        $this->urlStack[] = '/favicon.ico';
        $this->urlStack[] = '/apple-touch-icon.png';
        $this->urlStack[] = '/apple-touch-icon-precomposed.png';
        $this->urlStack[] = '/apple-touch-icon-57x57-precomposed.png';
        $this->urlStack[] = '/apple-touch-icon-72x72-precomposed.png';
        $this->urlStack[] = '/apple-touch-icon-114x114-precomposed.png';

        while($this->urlStack) {
            $url = array_shift($this->urlStack);
            $return = $this->processUrl($url);
            if (!$return) {
                continue;
            }
            foreach($return['urls'] as $subUrl) {
                if (!empty($this->fourOFours[$subUrl])) {
                    $this->fourOFours[$subUrl][] = $url;
                }
                if (file_exists($this->outputDir . $subUrl) || in_array($subUrl, $this->urlStack)) {
                    continue;
                }
                $this->urlStack[] = $subUrl;
            }
        }
    }

    protected function processUrl($url) {
        $this->out("Processing $url");
        $webFile = WWW_ROOT . substr($url, 1);
        if (file_exists($webFile) && is_file($webFile)) {
            $out = file_get_contents(WWW_ROOT . $url);
        } else {
            try {
                $out = $this->requestAction($url, array('return', 'bare' => false));
            } catch(Exception $e) {
                $this->fourOFours[$url] = array();
                return;
            }
        }

        if (!preg_match('@\.\w{3,4}$@', $url) === '/') {
            $url = rtrim($url, '/') . '/index.html';
        }
        if (!is_dir($this->outputDir . DS . dirname($url))) {
            mkdir($this->outputDir . DS . dirname($url), 0777, true);
        }
        file_put_contents($this->outputDir . $url, $out);
        if (substr($url, -5) === '.html') {
            $this->compressHtml($this->outputDir . $url, $out);
        }

        $urls = array();
        preg_match_all('@(?:src|href)=(["\'])(/[^/]\S+?)(#\S*)?\1@', $out, $matches);
        if ($matches) {
            $urls += $matches[2];
        }

        return array(
            'contents' => $out,
            'urls' => array_unique($urls)
        );
    }

    protected function compressHtml($file, &$out) {
        $file = escapeshellarg($file);
        $command = "java -jar Vendor/h5bp/build/tools/htmlcompressor-1.4.3.jar --compress-js --compress-css -o $file $file";
        exec($command);
        $out = file_get_contents($file);
    }
}
