	<div id="navigation">
		<?php
		foreach($config['navigation'] as $link){
			?>
			<a href="<?php echo $config['projectURL'];?><?php echo $link['url'];?>" <?php echo ((str_replace('.php', '.html', '/'.$page) == $link['url']) ? 'class="current"' : '');?>><?php echo $link['text'];?></a>
			<?php
		}
		?>
	</div>