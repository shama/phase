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

    <link href="<?php echo Configure::read('Phase.rss.url') ?>" rel="alternate" title="<?php echo Configure::read('Phase.rss.title') ?>" type="application/atom+xml">

    <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->

    <!-- All JavaScript at the bottom, except this Modernizr build incl. Respond.js
         Respond is a polyfill for min/max-width media queries. Modernizr enables HTML5 elements & feature detects;
         for optimal performance, create your own custom Modernizr build: www.modernizr.com/download/ -->
    <script src="/js/libs/modernizr-2.0.6.min.js"></script>
</head>

<body id="<?php echo $this->name ?>" class="<?php echo $this->action ?>">
  <div class="site">
    <div class="title">
      <a href="/"><?php echo Configure::read('Phase.site.name') ?></a>
      <a class="extra" href="/">home</a>
    </div>

    <header>
        <h1 class="title"><?php echo $title_for_layout ?></h1>
        <?php if (!empty($postDate)) { ?>
        <h3 class="date"><?php echo strftime('%e %B, %G', $postDate) ?></h3>
        <?php } ?>
        <p class="meta">
        </p>
        <a href="#disqus_thread">Show comments</a>
    </header>

    <div role="main">
        <?php echo $this->Session->flash(); ?>
        <?php echo $content_for_layout; ?>
    </div>

    <div class="footer">
      <div class="contact">
        <p>
          Andy Dawson<br />
		  andydawson76@<span style="display:none">null</span>gmail.com
        </p>
      </div>
      <div class="contact">
        <p>
          <a href="http://github.com/AD7six/">github.com/AD7six</a><br />
          <a href="http://twitter.com/AD7six/">twitter.com/AD7six</a><br />
        </p>
      </div>
      <div class="rss">
        <a href="<?php echo Configure::read('Phase.rss.url') ?>">
          <img src="/img/feed/feed-icon-28x28.png" alt="Subscribe to RSS Feed" />
        </a>
      </div>
    </div>
  </div>

    <!-- JavaScript at the bottom for fast page loading -->

    <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="/js/libs/jquery-1.7.0.min.js"><\/script>')</script>


    <!-- scripts concatenated and minified via build script -->
    <script defer src="/js/plugins.js"></script>
    <script defer src="/js/script.js"></script>
    <!-- end scripts -->


    <!-- Asynchronous Google Analytics snippet. Change UA-XXXXX-X to be your site's ID.
             mathiasbynens.be/notes/async-analytics-snippet -->
    <script>
        var _gaq=[['_setAccount','<?php echo Configure::read('Phase.analytics.code') ?>'],['_trackPageview']];
        (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
        g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
        s.parentNode.insertBefore(g,s)}(document,'script'));
    </script>

  <script type="text/javascript">
    var disqus_shortname = '<?php echo Configure::read('Phase.disqus.shortname') ?>';
  (function () {
    var s = document.createElement('script'); s.async = true;
    s.src = 'http://disqus.com/forums/<?php echo Configure::read('Phase.disqus.shortname') ?>/count.js';
    (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
  }());
  </script>
  <script type="text/javascript">
  (function() {
   var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
   dsq.src = 'http://<?php echo Configure::read('Phase.disqus.shortname') ?>x.disqus.com/embed.js';
   (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
  })();
</script>

    <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you want to support IE 6.
             chromium.org/developers/how-tos/chrome-frame-getting-started -->
    <!--[if lt IE 7 ]>
        <script defer src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
        <script defer>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
    <![endif]-->

</body>
</html>
