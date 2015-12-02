<?php
$site['title'] = 'Register';

$fields = array(
	'firstName' => array('name' => 'First Name'),
	'lastName' => array('name' => 'Last Name'),
	'email' => array('name' => 'E-Mail'),
	'password' => array('name' => 'Password', 'isPassword' => true),
	'retypepassword' => array('name' => 'Retype Password', 'isPassword' => true),
);

$message = '';
if(isset($_POST['submit'])){
	$errors = array();
	foreach($_POST as $key => $val){
		if($fields[$key] == ''){
			unset($_POST[$key]);
		}
	}
	foreach($fields as $key => $field){
		if(trim($_POST[$key]) == ''){
			$errors[] = 'Please provide the <i>'.$field['name'].'</i>.';
		}
	}
	if(count($errors) == 0){
		if(strlen($_POST['password']) < 6){
			$errors[] = 'Please enter a password with at least 6 characters.';
		}
		else if($_POST['password'] != $_POST['retypepassword']){
			$errors[] = 'Passwords don\'t match. Please retype your password correctly.';
		}
	}
	if(count($errors) == 0){
		$user = mysql_fetch_assoc($db->query("select * from users where email = '".mysql_real_escape_string($_POST['email'])."'"));
		if($user['id'] != ''){
			$errors[] = 'There is already a user having this email address.';
		}
	}
	if(count($errors) == 0){
		$db->query("insert into users set firstName = '".mysql_real_escape_string($_POST['firstName'])."', lastName = '".mysql_real_escape_string($_POST['lastName'])."', email = '".mysql_real_escape_string($_POST['email'])."', password = '".mysql_real_escape_string(crypt($_POST['password']))."', registered = '".time()."', enabled = '0'");
		$lastId = mysql_insert_id();
		$admin = mysql_fetch_assoc($db->query("SELECT email FROM users WHERE isAdmin = 1 LIMIT 1"));
		@mail($admin['email'], $config['projectName'].': A new user account has been created', "Hi,\n\n".$_POST['firstName']." ".$_POST['lastName']." has created an account with the following email address: ".$_POST['email'].". Use the following link to activate the user account: ".$config['projectURL']."administration/users.html?id=".$lastId."&action=enable", 'Content-Type: text/plain\n');
		$message = createMessage('You have been succesfully registered to '.$config['projectName'].'!', 'confirm');
		unset($_POST);
	}
	else{
		$message = createMessage(implode('<br />', $errors));
	}
}
?>
<h2><?php echo $site['title'];?></h2>
<?php echo $message;?>
<form action="" method="post">
 <?php
 foreach($fields as $key => $field){
	?>
	<div class="row">
		<label for="setting_<?=$key?>"><?=$field['name']?>:</label>
		<?php if(!$field['isHTML']){ ?>
			<input type="<?=(($field['isPassword']) ? 'password' : 'text')?>" id="setting_<?=$key?>" name="<?=$key?>" value="<?=((isset($_POST[$key])) ? $_POST[$key] : $config[$key])?>" />
		<?php } else { ?>
			<textarea id="setting_<?=$key?>" name="<?=$key?>"><?=((isset($_POST[$key])) ? $_POST[$key] : $config[$key])?></textarea>
		<?php } ?>
	</div>
	<?php
}
 ?>
	<div class="row">
		<input class="submit" type="submit" name="submit" value="Register" />
	</div>
</form>