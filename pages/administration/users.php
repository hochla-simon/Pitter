<?php
$site['title'] = 'User administration';
?>
<h2><?php echo $site['title'];?></h2>
<?php
if($_GET['action'] == 'delete'){
	$user = mysql_fetch_assoc($db->query("select * from users where id = '".mysql_real_escape_string($_GET['id'])."'"));
	if($user['id'] != '' and $user['isAdmin'] != '1'){
		mysql_query("delete from users where id = '".mysql_real_escape_string($_GET['id'])."'") or die(mysql_error());
		echo createMessage('The user was successfully deleted.', 'confirm');
	}
	else{
		echo createMessage('An error occurred.');
	}
}
else if($_GET['action'] == 'enable'){
    $user = mysql_fetch_assoc($db->query("select * from users where id = '".mysql_real_escape_string($_GET['id'])."'"));
	if($user['id'] != '' and $user['enabled'] != '1'){
		mysql_query("update users set enabled = '1' where id = '".mysql_real_escape_string($_GET['id'])."'") or die(mysql_error());
		mail($user['email'], $config['projectName'].': Your account has been activated', "Hi,\n\nyour account on ".$config['projectName']." has been activated some seconds ago. You can now log in and use the service:\n".$config['projectURL']."\users\login.html", 'Content-Type: text/plain\n');
		echo createMessage('The user was successfully enabled.', 'confirm');
	}
	else{
		echo createMessage('An error occurred.');
	}
}
else if($_GET['action'] == 'login'){
	$user = mysql_fetch_assoc($db->query("select * from users where id = '".mysql_real_escape_string($_GET['id'])."'"));
	if($user['id'] != '' and $user['enabled'] != '1'){
		$_SESSION['id'] = $user['id'];
		redirect_to($config['projectURl'].'users/profile.html');
	}
	else{
		echo make_error('An error occurred.');
	}
}
?>
<p>
 <a href="<?php echo $config['projectURL'];?>administration/createUser.html">Create new user</a>
</p>
<?php
$v = $_GET['v'];
if($v == '')
         $v = 0;
$amount = mysql_fetch_assoc($db->query("select count(id) as num from users where ".(($_GET['keywords'] != '') ? "(".get_search_cols($_GET['keywords'], array('users')).") and " : "")." 1=1 ".(($_GET['id'] != '' and $_GET['action'] != 'delete') ? " and id = '".mysql_real_escape_string($_GET['id'])."'" : "")));
$site_handler = site_handler($config['projectURl'].'administration/users.html?v={v}&amp;keywords='.urlencode($_GET['keywords']), $config['projectURl'].'administration/users.html?keywords='.urlencode($_GET['keywords']), $amount['num'], $v, '', 30);
echo $site_handler;
?> |
<form method="get" action="" class="searchForm"><b>Search:</b> <input type="text" name="keywords" value="<?=$_GET['keywords']?>" /> <input type="submit" name="search" value="search..." /></form>
<table width="100%"class="administrationTable">
 <tr>
  <td>ID</td>
  <td>Registered</td>
  <td>Firstname</td>
  <td>Lastname</td>
  <td>eMail</td>
  <td></td>
 </tr>
 <?php
 $result = $db->query("select * from users where ".(($_GET['keywords'] != '') ? "(".get_search_cols($_GET['keywords'], array('users')).") and " : "")." 1=1 ".(($_GET['id'] != '' and $_GET['action'] != 'delete') ? " and id = '".mysql_real_escape_string($_GET['id'])."'" : "")." order by id desc limit ".$v.",30");
 $i = 0;
 while($row = mysql_fetch_assoc($result)){
         ?>
         <tr class="<?php echo (($row['enabled'] != '1') ? 'disabled' : (($i % 2 == 0) ? 'even' : 'odd'));?>">
          <td><b><?php echo $row['id'];?></b></td>
          <td><?php echo date("d.m.Y H:i", $row['registered']);?></td>
          <td><b><?php echo $row['firstName'];?></b></td>
          <td><b><?php echo $row['lastName'];?></b></td>
          <td><a href="mailto:<?php echo $row['email'];?>"><?php echo $row['email'];?></a></td>
          <td><a href="<?php echo $config['projectURL'];?>administration/editUser.html?id=<?php echo $row['id'];?>">edit user</a><?php if($row['enabled'] != '1'):?> | <a href="<?php echo $config['projectURL'];?>administration/users.html?action=enable&amp;id=<?php echo $row['id'];?>" onclick="return confirm('Do you want to enable this user?');">enable user</a><?php endif;?><?php if($row['isAdmin'] != '1'):?> | <a href="<?php echo $config['projectURL'];?>administration/users.html?action=delete&amp;id=<?php echo $row['id'];?>" onclick="return confirm('Do you really want to delete this user?');">delete user</a> | <a href="<?php echo $config['projectURL'];?>administration/users.html?action=login&amp;id=<?php $row['id'];?>">log in as user</a><?php endif;?></td>
         </tr>
         <?php
         $i++;
 }
 ?>
</table>
<?php echo $site_handler;?>  