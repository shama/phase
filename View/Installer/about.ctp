---
layout: home
title: Welcome to phase
---

<?php if (!is_dir(Configure::read('PhaseRoot'))): ?>

No Site contents could be found - [create site now?](<?php echo $this->Html->url('/go') ?>)
<?php endif;

echo file_get_contents(APP . 'README.md');
