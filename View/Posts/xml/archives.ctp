---
layout: xml/atom
---

<?php foreach($posts as $file): $post = $this->Post->data($file); ?>
 <entry>
   <title><?php echo $post['title'] ?></title>
   <link href="<?php echo Configure::read('Phase.rss.domain') . $post['url']?>"/>
   <updated><?php echo strftime('%Y-%m-%dT%H:%M:%s+%z', $post['date']) ?></updated>
   <id><?php echo Configure::read('Phase.rss.domain') . $post['url']?></id>
   <content type="html"><![CDATA[ <?php echo htmlspecialchars($post['contents'], 16, 'UTF-8')  ?>']]></content>
 </entry>
<?php endforeach; ?>
