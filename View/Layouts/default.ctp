<?php echo $this->element('head', compact('title_for_layout')) ?>
<body id="<?php echo $this->name ?>" class="<?php echo $this->action ?>">
  <div class="site">
    <?php echo $this->element('header') ?>

    <div role="main">
        <?php echo $this->Session->flash(); ?>
        <?php echo $content_for_layout; ?>
    </div>

    <?php echo $this->element('footer') ?>
    <?php echo $this->element('scripts') ?>
  </div>
</body>
</html>
