	<div id="navigation">
		<?php
		foreach($config['navigation'] as $link){
			?>
			<a href="<?php echo $config['projectURL'];?><?php echo $link['url'];?>" <?php echo (('/'.$page == $link['url']) ? 'class="current"' : '');?>><?php echo $link['text'];?></a>
			<?php
		}
		?>
	</div>