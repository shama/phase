---
layout: xml/atom
---

<?php foreach($posts as $post): $post = $this->Post->data($post); ?>
 <entry>
   <title><?php echo $post['title'] ?></title>
   <link href="<?php echo Configure::read('Phase.site.domain') . $post['url']?>"/>
   <updated><?php echo strftime('%Y-%m-%dT%H:%M:%s+%z', $post['date']) ?></updated>
   <id><?php echo Configure::read('Phase.site.domain') . $post['url']?></id>
   <content type="html"><![CDATA[<?php echo $post['contents'] ?>']]></content>
 </entry>
<?php endforeach; ?>
