<?php
$site['title'] = 'Administration';
?>
<h2><?php echo $site['title'];?></h2>
<table>
   <tr>
    <td><a href="<?php echo $config['projectURL']?>administration/users.html" class="administrationMenu">Manage Users</a></td>
	<td><a href="<?php echo $config['projectURL']?>administration/settings.html" class="administrationMenu">Edit Settings</a></td>
   </tr>
</table>