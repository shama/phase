<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">

 <title><?php echo Configure::read('Phase.feed.title') ?></title>
 <link href="http://AD7six.com/atom.xml" rel="self"/>
 <link href="http://AD7six.com/"/>
 <?php $lastPost = $this->Post->data($posts[0]); ?>
 <updated><?php echo strftime('%Y-%m-%dT%H:%M:%s+%z', $lastPost['date']) ?></updated>
 <id><?php echo Configure::read('Phase.feed.id') ?></id>
 <author>
   <name><?php echo Configure::read('Phase.feed.author') ?></name>
   <email><?php echo Configure::read('Phase.feed.email') ?></email>
 </author>

 <?php echo $content_for_layout; ?>

</feed>
