<?php

App::uses('AppShell', 'Console/Command');
App::uses('Folder', 'Utility');

/**
 * BuildTask
 */
class BuildTask extends AppShell {

    /**
     * outputDir
     *
     * Where to write the static version of this application
     */
    public $outputDir = 'publish';

    public $optimizations = array(
        'html' => array(
            'concatenateCss',
            'concatenateJs',
            'compressHtml'
        ),
        'js' => array(
            'compressJs'
        ),
        'css' => array(
            'compressCss'
        )
    );

    /**
     * fourOFours
     *
     * Array of urls that returned errors while crawling
     */
    protected $fourOFours = array();

    /**
     * concatenatedStack
     *
     * Array of id => hashs used to know which asset packets have already been processed
     */
    protected $concatenatedStack = array();

    /**
     * urlStack
     *
     * The urls to crawl
     */
    protected $urlStack = array();

    /**
     * seedUrls
     *
     * Urls used when pseudo-crawling the site
     */
    protected $seedUrls = array(
        '/',
        '/.htaccess',
        '/robots.txt',
        '/favicon.ico',
        '/apple-touch-icon.png',
        '/apple-touch-icon-precomposed.png',
        '/apple-touch-icon-57x57-precomposed.png',
        '/apple-touch-icon-72x72-precomposed.png',
        '/apple-touch-icon-114x114-precomposed.png',
        '/atom.xml'
    );

/**
 * Disable caching and disable debug for building.
 */
	public function startup() {
		Configure::write('debug', 0);
		Configure::write('Cache.disable', 1);
		parent::startup();
	}

    /**
     * Wipe the output diretory and repopulate it using what you can see
     * browsing your development install as the input
     */
	public function execute() {
        if (!empty($this->args[0])) {
            $this->outputDir = $this->args[0];
        }

        touch(TMP . 'building');
        exec('rm -rf ' . escapeshellarg($this->outputDir));
        mkdir($this->outputDir . '/css', 0777, true);
        mkdir($this->outputDir . '/img', 0777, true);
        mkdir($this->outputDir . '/js', 0777, true);

        $root = Configure::read('PhaseWebroot');
        $offset = strlen($root) - 1;
        $folder = new Folder($root);
        $files = $folder->findRecursive();
        foreach($files as $file) {
            $url = substr($file, $offset);
            $this->processUrl($url);
        }

        $this->recurse();

        if ($this->fourOFours) {
            $this->err("<warning>404s!</warning>");
            foreach($this->fourOFours as $url => $referers) {
                $this->out("\t$url");
            }
        }
        unlink(TMP . 'building');
	}

	public function getOptionParser() {
		$parser = parent::getOptionParser();
        $parser->description(array(
            __d('phase', 'Crawl application and create a static version of the result'),
        ))->addArgument('outputFolder', array(
            'help' => __d('phase', 'Where to put the generated files'),
            'required' => false
        ));
        return $parser;
	}

    /**
     * recurse
     *
     * Starting with a few seed urls - crawl the cake application following all the urls
     * that can be found. for everything that's linked to compress it and write out to the
     * output folder.
     *
     * Track and report 404s so that they can be corrected
     *
     * @param array $stack seed urls - if not passed the property seedUrls is used
     */
    protected function recurse($stack = null) {
        if ($stack === null) {
            $this->urlStack = $this->seedUrls;
        } else {
            $this->urlStack = $stack;
        }

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

    /**
     * processUrl
     *
     * Request the url from the application, and if it's html, css or js minify
     * the contents.
     * Write the contents to the equivalent url/location in the output folder
     *
     * @param mixed $url
     */
    protected function processUrl($url) {
        $this->out("Processing $url");

        $contents = $this->getContents($url);
        if ($contents === false) {
            return;
        }

        if (!preg_match('@\.\w{2,4}$@', $url)) {
            $basename = basename($url);
            if ($basename && $basename[0] === '.') {
            } else {
                $url = rtrim($url, '/') . '/index.html';
            }
        }
        if (!is_dir($this->outputDir . dirname($url))) {
            mkdir($this->outputDir . dirname($url), 0777, true);
        }

        $dot = strrpos($url, '.');
        $ext = substr($url, $dot + 1);
        if (!empty($this->optimizations[$ext])) {
            foreach($this->optimizations[$ext] as $method) {
                $this->$method($contents);
            }
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

    /**
     * compressCss
     *
     * if a filename is passed - it is used as the input, otherwise the contents are used
     * In both cases the contents are updated
     *
     * @param mixed $contents
     * @param string $file
     */
    protected function compressCss(&$contents, $file = '') {
        if (!$file) {
            $file = '/tmp/compressthis.css';
            file_put_contents($file, $contents);
        }
        $command = "java -jar Vendor/h5bp/build/tools/yuicompressor-2.4.5.jar --type css -o $file $file";
        exec($command);
        $contents = file_get_contents($file);
    }

    /**
     * compressHtml
     *
     * if a filename is passed - it is used as the input, otherwise the contents are used
     * In both cases the contents are updated
     *
     * @param string $contents
     * @param string $file
     */
    protected function compressHtml(&$contents, $file = '') {
        if (!$file) {
            $file = '/tmp/compressthis.html';
            file_put_contents($file, $contents);
        }
        $command = "java -jar Vendor/h5bp/build/tools/htmlcompressor-1.4.3.jar --compress-js --compress-css -o $file $file";
        exec($command);
        $contents = file_get_contents($file);
    }

    /**
     * compressJs
     *
     * if a filename is passed - it is used as the input, otherwise the contents are used
     * In both cases the contents are updated
     *
     * @param string $contents
     * @param string $file
     */
    protected function compressJs(&$contents, $file = '') {
        if (!$file) {
            $file = '/tmp/compressthis.js';
            file_put_contents($file, $contents);
        }
        $command = "java -jar Vendor/h5bp/build/tools/yuicompressor-2.4.5.jar --type js -o $file $file";
        exec($command);
        $contents = file_get_contents($file);
    }

    /**
     * concatenateCss
     *
     * Parse out any local stylesheet links and concatenate them into packets
     * And replace all matches with a single concatenated and minified css file
     *
     * @param mixed $html
     */
    protected function concatenateCss(&$html) {
        $copy = preg_replace('@<!--.*?-->@s', '', $html);
        preg_match_all('@<link.*?rel="stylesheet".*? href="(/[^/].*?\.css)".*?>@', $copy, $matches);
        if (!$matches) {
            return;
        }
        $packets = array();
        foreach($matches[0] as $i => $match) {
            $class = 'default';
            preg_match('@class=(["\'])(.*?)\1@', $match, $classMatch);
            if ($classMatch) {
                $class = $classMatch[2];
            }
            $packets[$class][] = $matches[1][$i];
        }

        $replace = '';
        foreach($packets as $packet) {
            $files = array_unique($packet);

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

            $replace .= '<link rel="stylesheet" href="/css/' . $hash . '.min.css">';
        }

        $lastFile = array_pop($matches[0]);
        foreach($matches[0] as $match) {
            $html = str_replace($match, '', $html);
        }
        $html = str_replace($lastFile, $replace, $html);
    }

    /**
     * concatenateJs
     *
     * Parse out any local scripts, concatenate them into packets, minify and replace references
     * The class atribute is used to allow for the possibility of bundling js files into multiple
     * packets; if a script tag is not text/javascript it's ignored
     *
     * @param string $html
     */
    protected function concatenateJs(&$html, $section = 'both') {
        if ($section === 'both') {
            $split = strpos($html, '<body');
            if (!$split) {
                return;
            }
            $head = $headOriginal = substr($html, 0, $split);
            $body = $bodyOriginal = substr($html, $split);
            $this->concatenateJs($head, 'head');
            $this->concatenateJs($body, 'body');
            $html = $head . $body;
            return;
        }
        $copy = preg_replace('@<!--.*?-->@s', '', $html);
        preg_match_all('@<script.*?src="(/[^/].*?\.js)".*?></script>@', $copy, $matches);
        if (!$matches) {
            return;
        }
        $packets = array();
        foreach($matches[0] as $i => $match) {
            preg_match('@type=(["\'])(.*?)\1@', $match, $typeMatch);
            if ($typeMatch && strtolower($typeMatch[2]) !== 'text/javascript') {
                continue;
            }
            $class = 'default';
            preg_match('@class=(["\'])(.*?)\1@', $match, $classMatch);
            if ($classMatch) {
                $class = $classMatch[2];
            }
            $packets[$class][] = $matches[1][$i];
        }

        $replace = '';
        foreach($packets as $packet) {
            $files = array_unique($packet);

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

            $replace .= '<script async src="/js/' . $hash . '.min.js"></script>';
        }

        $lastFile = array_pop($matches[0]);
        foreach($matches[0] as $match) {
            $html = str_replace($match, '', $html);
        }
        $html = str_replace($lastFile, $replace, $html);
    }

    /**
     * getContents
     *
     * Simulate requesting the url with a browser - checks the webroot and then the app
     *
     * @param string $url
     */
    protected function getContents($url = '/') {
        $root = rtrim(Configure::read('PhaseWebroot'), DS);
        $rootFile = $root . $url;
        if (file_exists($rootFile) && is_file($rootFile)) {
            return file_get_contents($root . $url);
        }

        $webFile = WWW_ROOT . substr($url, 1);
        if (file_exists($webFile) && is_file($webFile)) {
            return file_get_contents(WWW_ROOT . $url);
        }

        try {
            return $this->requestAction($url, array('return', 'bare' => false));
        } catch(Exception $e) {
            $this->fourOFours[$url] = array();
        }
        return false;
    }


}
