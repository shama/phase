Phase ![Project status](http://stillmaintained.com/AD7six/phase.png?201112291710)
====================================================================

Phase is a static site builder similar to the awesome jekyll. It uses (by default) markdown and
YFM (Yaml front matter) such that your posts are largely interchangeable between a jekyl and phase
install. By using best practices outlined in the h5bp project, it generates a highly-optimized
static version of your site.

Dependencies
------------

Phase dependencies:

* PHP 5.3.0+
* java

If java is not available, phase can still be used by using the `--no-compression` flag.

Installation
------------

Phase uses cakephp 2.0+, and also makes use of a couple of other projects. To install from scratch:

    mkdir hmmm-yeah.over-here
    cd hmmm-yeah.over-here
	git clone git://github.com/cakephp/cakephp.git
	cd cakephp
	git clone git://github.com/AD7six/phase.git
	cd phase
	git submodule update --init --recursive
	copy Config/core.php.default Config/core.php
	cp -R Skel Site

Point your browser at hmm-yeah.over-here/phase/webroot and make sure it doesn't look broken.

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
in the built version of your site. A few urls from the h5bp project are used as seeds to crawl the
rest of your content.

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

There are three subcommands, and they are listed in the order they are intended to be used. Each
has it's own specific help.

Roadmap
-------

A few things are currently missing but planned for:

* Add sitemap
* Add CSS sprite building
* Add image optimizations
* Add a js search
* Ignore drafts when deploying
* Move code around a bit (make plugins where appropriate)
* Add test cases

History
-------

18/12/2011 - v0.1 Initial release
