    <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="/js/libs/jquery-1.7.0.min.js"><\/script>')</script>


    <!-- scripts concatenated and minified via build script -->
    <script async defer src="/js/plugins.js"></script>
    <script async defer src="/js/script.js"></script>
    <!-- end scripts -->


    <script>
        var _gaq=[
            ['_setAccount','<?php echo Configure::read('Phase.analytics.code') ?>'],
            ['_trackPageview']
        ];
    </script>
    <script async src='//www.google-analytics.com/ga.js' ></script>

    <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you want to support IE 6.
             chromium.org/developers/how-tos/chrome-frame-getting-started -->
    <!--[if lt IE 7 ]>
        <script async defer src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
        <script async defer>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
    <![endif]-->

