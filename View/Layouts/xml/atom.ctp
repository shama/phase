<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">

 <title><?php echo Configure::read('Phase.feed.title') ?></title>
 <link href="<?php echo Configure::read('Phase.site.domain') ?>atom.xml" rel="self"/>
 <link href="<?php echo Configure::read('Phase.site.domain') ?>"/>
 <updated><?php echo strftime('%Y-%m-%dT%H:%M:%s+%z', $posts[0]['date']) ?></updated>
 <id><?php echo Configure::read('Phase.feed.id') ?></id>
 <author>
   <name><?php echo Configure::read('Phase.feed.author') ?></name>
   <email><?php echo Configure::read('Phase.feed.email') ?></email>
 </author>

 <?php echo $content_for_layout; ?>

</feed>
