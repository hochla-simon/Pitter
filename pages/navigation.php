  <div id="navigation">
   <?php
   foreach($config['navigation'] as $link){
	?>
		<a href="<?=preg_replace('!\.php$!', '.html', $link['url'])?>" <?=(('/'.$page == $link['url']) ? 'class="current"' : '')?>><?=$link['text']?></a>
	<?php
	}
   ?>
  </div>