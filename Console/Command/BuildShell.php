<?php

App::uses('AppShell', 'Console/Command');

class BuildShell extends AppShell {

    protected $outputDir = 'publish';

    protected $fourOFours = array();

    protected $concatenatedStack = array();

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
        mkdir($this->outputDir . '/css', 0777, true);
        mkdir($this->outputDir . '/img', 0777, true);
        mkdir($this->outputDir . '/js', 0777, true);

        $this->recurse();

        if ($this->fourOFours) {
            $this->err("<warning>404s!</warning>");
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

        $contents = $this->getContents($url);
        if ($contents === false) {
            return;
        }

        if (!preg_match('@\.\w{3,4}$@', $url)) {
            $url = rtrim($url, '/') . '/index.html';
        }
        if (!is_dir($this->outputDir . dirname($url))) {
            mkdir($this->outputDir . dirname($url), 0777, true);
        }
        if (substr($url, -5) === '.html') {
            $this->concatenateCss($contents);
            $this->concatenateScripts($contents);
            $this->compressHtml($contents);
        }
        file_put_contents($this->outputDir . $url, $contents);

        $urls = array();
        preg_match_all('@(?:src|href)=(["\'])(/[^/]\S+?)(#\S*)?\1@', $contents, $matches);
        if ($matches) {
            $urls += $matches[2];
        }

        return array(
            'contents' => $contents,
            'urls' => array_unique($urls)
        );
    }

    protected function compressCss(&$contents, $file = '') {
        if (!$file) {
            $file = '/tmp/compressthis.css';
            file_put_contents($file, $contents);
        }
        $command = "java -jar Vendor/h5bp/build/tools/yuicompressor-2.4.5.jar --type css -o $file $file";
        exec($command);
        $contents = file_get_contents($file);
    }

    protected function compressHtml(&$contents, $file = '') {
        if (!$file) {
            $file = '/tmp/compressthis.html';
            file_put_contents($file, $contents);
        }
        $command = "java -jar Vendor/h5bp/build/tools/htmlcompressor-1.4.3.jar --compress-js --compress-css -o $file $file";
        exec($command);
        $contents = file_get_contents($file);
    }

    protected function compressJs(&$contents, $file = '') {
        if (!$file) {
            $file = '/tmp/compressthis.js';
            file_put_contents($file, $contents);
        }
        $command = "java -jar Vendor/h5bp/build/tools/yuicompressor-2.4.5.jar --type js -o $file $file";
        exec($command);
        $contents = file_get_contents($file);
    }

    protected function concatenateCss(&$html) {
        $copy = preg_replace('@<!--.*?-->@s', '', $html);
        preg_match_all('@<link rel="stylesheet" href="(/[^/].*?)">@', $copy, $matches);
        if (!$matches) {
            return;
        }

        $files = array_unique($matches[1]);

        $contents = '';
        $id = implode($files, ':');

        if (empty($this->concatenatedStack[$id])) {
            foreach($files as $url) {
                $contents .= $this->getContents($url);
            }
            $hash = md5($contents);
            $this->concatenatedStack[$id] = $hash;
            file_put_contents($this->outputDir . "/css/$hash.min.css", $contents);
            $this->compressCss($contents, $this->outputDir . "/css/$hash.min.css");
        } else {
            $hash = $this->concatenatedStack[$id];
        }

        $lastFile = array_pop($matches[0]);
        $html = str_replace($lastFile, '<link rel="stylesheet" href="/css/' . $hash . '.min.css">', $html);
        foreach($matches[0] as $match) {
            $html = str_replace($match, '', $html);
        }
    }

    protected function concatenateScripts(&$html) {
        $copy = preg_replace('@<!--.*?-->@s', '', $html);
        preg_match_all('@<script defer src="(/[^/].*?)"></script>@', $copy, $matches);
        if (!$matches) {
            return;
        }

        $files = array_unique($matches[1]);

        $contents = '';
        $id = implode($files, ':');

        if (empty($this->concatenatedStack[$id])) {
            foreach($files as $url) {
                $contents .= $this->getContents($url);
            }
            $hash = md5($contents);
            $this->concatenatedStack[$id] = $hash;
            file_put_contents($this->outputDir . "/js/$hash.min.js", $contents);
            $this->compressJs($contents, $this->outputDir . "/js/$hash.min.js");
        } else {
            $hash = $this->concatenatedStack[$id];
        }

        $lastFile = array_pop($matches[0]);
        $html = str_replace($lastFile, '<script defer src="/js/' . $hash . '.min.js"></script>', $html);
        foreach($matches[0] as $match) {
            $html = str_replace($match, '', $html);
        }
    }

    protected function getContents($url) {
        $webFile = WWW_ROOT . substr($url, 1);
        if (file_exists($webFile) && is_file($webFile)) {
            $contents = file_get_contents(WWW_ROOT . $url);
        } else {
            try {
                $contents = $this->requestAction($url, array('return', 'bare' => false));
            } catch(Exception $e) {
                $this->fourOFours[$url] = array();
                return;
            }
        }

        return $contents;
    }

}
