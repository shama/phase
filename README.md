Phase
=====

Phase is a static site builder similar to the awesome jekyll. It uses (by default) markdown and
YFM (Yaml front matter) such that your posts are largely interchangable between a jekyl and phase
install. By using best practices outlined in the h5bp project, it generates a highly-optimized
static version of your site, including html compression, javascript and css compression and
concatenation.

Dependencies
------------

Phase dependencies:

* PHP 5.3.0+
* java

If java is not available, phase can still be used by disabling frontend optimizations (or possibly
by closing your eyes and holding your breath while ignoring the reams of errors you'll see when
generating a build)

Installation
------------

Phase requires cakephp 2.0+, and has a couple of dependencies. To install from scratch:

    mkdir hmmm-yeah.over-here
    cd hmmm-yeah.over-here
	git clone git://github.com/cakephp/cakephp.git
	cd cakephp
	git clone git://github.com/AD7six/phase.git
	cd phase
	git submodule update --init --recursive
	copy Config/core.php.default Config/core.php
	cp -R Skel Site

Point your browser at hmm-yeah.over-here/phase/webroot and make sure it doesn't look broken. Then:

Concepts
--------

Phase is a php application when running on your local machine, it is an essentially static site
when you deploy to your public server. Write whatever you want, when you're happy with your new
literary work of art, build and deploy.

Javascript and css files are automatically concatenated and compressed on build - no messing about.
The hash of the (resultant) contents is used for the filename - this means changes to your files
which have no impact on the resultant compressed file (formatting, comments) will not cause users
to re-download the same resource again if they have it in cache.

Only the contents of `PhaseRoot/Site/webroot` and any urls your application references are present
in the built version of your site. A urls from the h5bp project are used as seeds to crawl the rest
of your content.

Usage
-----

For a simple intro - just dive right into the cli:

	$ Console/cake phase
	Manage your phase-built site

	Usage:
	cake phase [subcommand] [-h] [-v] [-q]

	Subcommands:

	write   Create a new post.
	build   Generate a static version of your application.
	deploy  Copy files to public server.
	...

Roadmap
-------

A few things are currently missing but planned for:

* Add CSS image detection (parsing of url references in css)
* Add CSS image optimizations
* Add CSS sprite building
* Add test cases

History
-------

18/12/2011 - v0.1 Initial release