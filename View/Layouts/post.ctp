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

    <?php echo $this->Session->flash(); ?>
    <div role="main">
        <?php echo $content_for_layout; ?>
        <div id="disqus_thread"></div>
    </div>

    <?php echo $this->element('footer') ?>
    <?php echo $this->element('scripts') ?>
  </div>

  <script>
    var disqus_shortname = '<?php echo Configure::read('Phase.disqus.shortname') ?>';
    <?php if (!file_exists(TMP . 'building')): ?>
        var disqus_developer = 1; // developer mode is on
    <?php endif; ?>
  </script>
  <script async defer src="http://disqus.com/forums/<?php echo Configure::read('Phase.disqus.shortname') ?>/count.js"></script>
  <script async defer src="http://<?php echo Configure::read('Phase.disqus.shortname') ?>.disqus.com/embed.js"></script>
</body>
</html>
