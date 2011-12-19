<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">

 <title><?php echo Configure::read('Phase.rss.title') ?></title>
 <link href="http://AD7six.com/atom.xml" rel="self"/>
 <link href="http://AD7six.com/"/>
 <?php $lastPost = $this->Post->data($posts[0]); ?>
 <updated><?php echo strftime('%Y-%m-%dT%H:%M:%s+%z', $lastPost['date']) ?></updated>
 <id><?php echo Configure::read('Phase.rss.id') ?></id>
 <author>
   <name><?php echo Configure::read('Phase.rss.author') ?></name>
   <email><?php echo Configure::read('Phase.rss.email') ?></email>
 </author>

 <?php echo $content_for_layout; ?>

</feed>
