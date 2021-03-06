<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <title><?php echo $title_for_layout; ?></title>

    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta name="keywords" content="<?php echo $meta_keywords; ?>">
    <meta name="author" content="<?php echo $meta_author; ?>">

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/screen.css">
	<link rel="stylesheet" href="/css/syntax.css">

    <link href="<?php echo Configure::read('Phase.feed.url') ?>" rel="alternate" title="<?php echo Configure::read('Phase.feed.title') ?>" type="application/atom+xml">

    <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

    <!-- All JavaScript at the bottom, except this Modernizr build incl. Respond.js
         Respond is a polyfill for min/max-width media queries. Modernizr enables HTML5 elements & feature detects;
         for optimal performance, create your own custom Modernizr build: www.modernizr.com/download/ -->
    <!-- Need to run sync (blocking) so that the page doesn't flash after parsing -->
    <script src="/js/libs/modernizr-2.0.6.min.js"></script>
</head>

