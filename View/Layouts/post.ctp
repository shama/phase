<?php echo $this->element('head', compact('title_for_layout')) ?>
<body id="<?php echo $this->name ?>" class="<?php echo $this->action ?>">
  <div class="site">
    <?php echo $this->element('header') ?>

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

    <?php echo $this->element('footer') ?>
    <?php echo $this->element('scripts') ?>
  </div>

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
   dsq.src = 'http://<?php echo Configure::read('Phase.disqus.shortname') ?>.disqus.com/embed.js';
   (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
  })();
</script>
</body>
</html>
