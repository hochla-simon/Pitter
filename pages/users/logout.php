<?php
$site['title'] = 'Logout';
?>
<h2>Logout</h2>
<?php
if($_SESSION['id'] != ''){
	$_SESSION['id'] = '';
	@session_destroy();
	echo '<a href="'.$config['projectURL'].'/users/logout.html">Continue...</a><meta http-equiv="refresh" content=\"0; URL='.$config['projectURL'].'/users/logout.html"><script type="text/javascript">onload = top.location.href = \''.$config['projectURL'].'/users/logout.html\';</script>';
}
else{
    ?>
	<?php echo createMessage('Successfully logged out.', 'confirm');?>
	<p>
	 <br />
	 Back to <a href="<?php echo $config['projectURL'];?>/">home page</a>...
	</p>
    <?php
}
?>