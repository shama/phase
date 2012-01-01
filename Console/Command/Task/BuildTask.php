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
            'concatCss',
            'concatJs',
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
     * concatStack
     *
     * Array of id => hashes used to know which asset packets have already been processed
     */
    protected $concatStack = array();

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
     * Wipe the output directory and repopulate it using what you can see
     * browsing your development install as the input
     */
	public function execute() {
        if (!empty($this->args[0])) {
            $this->addUrl('/' . ltrim($this->args[0]));
        }

        if (!empty($this->params['output'])) {
            $this->outputDir = $this->params['output'];
        }

        if (!empty($this->params['no-optimize'])) {
            $this->optimizations = array();
        }
        if (!empty($this->params['no-compress'])) {
            foreach($this->optimizations as $ext => &$methods) {
                foreach($methods as $i => $method) {
                    if (strpos($method, 'compress') === 0) {
                        unset($methods[$i]);
                    }
                }
            }
        }
        if (!empty($this->params['no-concat'])) {
            foreach($this->optimizations as $ext => &$methods) {
                foreach($methods as $i => $method) {
                    if (strpos($method, 'concat') === 0) {
                        unset($methods[$i]);
                    }
                }
            }
        }

        touch(TMP . 'building');
        exec('rm -rf ' . escapeshellarg($this->outputDir));
        mkdir($this->outputDir . '/css', 0777, true);
        mkdir($this->outputDir . '/img', 0777, true);
        mkdir($this->outputDir . '/js', 0777, true);

        if (empty($this->urlStack)) {
            $this->urlStack = $this->seedUrls;
            $root = Configure::read('PhaseWebroot');
            $offset = strlen($root) - 1;
            $folder = new Folder($root);
            $files = $folder->findRecursive();
            foreach($files as $file) {
                $url = substr($file, $offset);
                $this->processUrl($url);
            }
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
        ))->addArgument('url', array(
            'help' => __d('phase', 'The seed url to crawl'),
            'required' => false
        ))->addOption('output', array(
            'help' => __d('phase', 'Where to put the generated files, defaults to "%s"', $this->outputDir),
            'required' => false
        ))->addOption('no-optimize', array(
            'help' => __d('phase', 'Disable all optimizations'),
            'boolean' => true
        ))->addOption('no-concat', array(
            'help' => __d('phase', 'Disable automatic css and js concatenation'),
            'boolean' => true
        ))->addOption('no-compress', array(
            'help' => __d('phase', 'Disable all file compression (minification)'),
            'boolean' => true
        ));
        return $parser;
	}

    /**
     * addUrl to the crawl stack
     *
     * Unless it's already been processed, or is in already in the stack
     *
     * @param mixed $url
     */
    protected function addUrl($url) {
        if (
            !empty($this->fourOFours[$url]) ||
            file_exists($this->outputDir . $url) ||
            in_array($url, $this->urlStack)
        ) {
            return false;
        }
        $this->urlStack[] = $url;
    }

    /**
     * compress css using yui compressor
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
     * compress html using html compressor
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
     * compress js using yui compressor
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
     * concatCss
     *
     * Parse out any local stylesheet links and concat them into packets
     *
     * @param string $html
     */
    protected function concatCss(&$html) {
        $this->concatReferences(
            $html,
            'css',
            '@<link.*?rel="stylesheet".*? href="(/[^/].*?\.css)".*?>@',
            '<link rel="stylesheet" href="/css/%hash%.min.css">'
        );
    }

    /**
     * concatJs - processing the head and body separately
     *
     * Parse out any local scripts, concat them into packets, minify and replace references
     * The class attribute is used to allow for the possibility of bundling js files into multiple
     * packets; if a script tag is not text/javascript it's ignored
     *
     * @param string $html
     */
    protected function concatJs(&$html, $section = 'both') {
        if ($section === 'both') {
            $split = strpos($html, '<body');
            if (!$split) {
                return;
            }
            $head = $headOriginal = substr($html, 0, $split);
            $body = $bodyOriginal = substr($html, $split);
            $this->concatJs($head, 'head');
            $this->concatJs($body, 'body');
            $html = $head . $body;
            return;
        }
        $this->concatReferences(
            $html,
            'js',
            '@<script.*?src="(/[^/].*?\.js)".*?></script>@',
            '<script async src="/js/%hash%.min.js"></script>'
        );
    }

    /**
     * Search for references to concat (css and js). The replace logic is common to both types
     * and potentially useful for other things in the future
     *
     * @param string $html
     * @param string $type
     * @param string $pattern
     * @param string $replaceTag
     */
    protected function concatReferences(&$html, $type, $pattern, $replaceTag) {
        $copy = preg_replace('@<!--.*?-->@s', '', $html);
        preg_match_all($pattern, $copy, $matches);
        if (!$matches) {
            return;
        }

        $packets = array();
        foreach($matches[0] as $i => $match) {
            if ($type === 'js') {
                preg_match('@type=(["\'])(.*?)\1@', $match, $typeMatch);
                if ($typeMatch && strtolower($typeMatch[2]) !== 'text/javascript') {
                    continue;
                }
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

            if (empty($this->concatStack[$id])) {
                foreach($files as $url) {
                    if ($type === 'css') {
                        $contents .= $this->getNormalizedCss($url);
                    } else {
                        $contents .= $this->getContents($url);
                    }
                }
                $hash = md5($contents);
                $url = "/$type/$hash.min.$type";
                $this->concatStack[$id] = $hash;
                file_put_contents($this->outputDir . $url, $contents);

                if (!empty($this->optimizations[$type])) {
                    foreach($this->optimizations[$type] as $method) {
                        $this->$method($contents, $this->outputDir . $url);
                    }
                }
            } else {
                $hash = $this->concatStack[$id];
            }

            $replace .= str_replace('%hash%', $hash, $replaceTag);
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

    /**
     * getNormalizedCss
     *
     * Get the css and normalize to an absolute url any url references
     * Therefore for /css/foo.css, it converts:
     *  img/foo.png to /css/img/foo.png
     *  ../img/foo.png to /img/foo.png
     *
     * @param string $url
     */
    protected function getNormalizedCss($url = '/') {
        $contents = $this->getContents($url);

        preg_match_all('@url\(["\']?(.*?)["\']?\)@', $contents, $matches);
        if ($matches[0]) {
        }
        $base = dirname($url) . '/';
        foreach($matches[1] as $i => $match) {
            if (strpos($match, '://') || strpos($match, '//') === 0) {
                continue;
            }
            if ($match[0] === '/') {
                $this->addUrl($match);
                continue;
            }

            $normalized = $base . $match;
            while(strpos($normalized, '../')) {
                $normalizedReplaced = preg_replace('@[^/\.]+/\.\./@', '', $normalized);
                if ($normalizedReplaced === $normalized) {
                    break;
                }
                $normalized = $normalizedReplaced;
            }
            $this->addUrl($normalized);
            $find = $matches[0][$i];

            /* TODO - where should this go
            if (strpos($normalized, '/css/') === 0) {
                $normalized = substr($normalized, 5);
            }
            */
            $replace = str_replace($match, $normalized, $find);
            $contents = str_replace($find, $replace, $contents);
        }

        return $contents;
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

        if (substr($url, -3) === 'css') {
            $contents = $this->getNormalizedCss($url);
        } else {
            $contents = $this->getContents($url);
        }

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
     * recurse
     *
     * Starting with a few seed urls - crawl the cake application following all the urls
     * that can be found. for everything that's linked to compress it and write out to the
     * output folder.
     *
     * Track and report 404s so that they can be corrected
     */
    protected function recurse() {
        if (!$this->urlStack) {
            $this->urlStack = $this->seedUrls;
        }

        while($this->urlStack) {
            $url = array_shift($this->urlStack);
            $return = $this->processUrl($url);
            if (!$return) {
                continue;
            }
            foreach($return['urls'] as $subUrl) {
                $this->addUrl($subUrl);
            }
        }
    }

}
