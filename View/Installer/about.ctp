---
layout: home
title: Welcome to phase
---

Welcome!
--------

Phase is a static site generator - similar to [jekyl](jekyllrb.com).

<?php if (!is_dir(Configure::read('PhaseRoot'))): ?>
No Site contents could be found - [create site now?](<?php echo $this->Html->url('/go') ?>)
<?php endif; ?>
