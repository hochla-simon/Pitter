<?php
$site['title'] = 'Recover Password';

$fields = array(
	'email' => array('name' => 'E-Mail Address')
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
		$user = mysql_fetch_assoc($db->query("select * from users where email = '".mysql_real_escape_string($_POST['email'])."'"));
		if($user['id'] == ''){
			$errors[] = 'No user having this email address could be found.';
		}
	}
	if(count($errors) == 0){
		$password = substr(md5(microtime()), 0, 10);
		$db->query("update users set password = '".mysql_real_escape_string(crypt($password))."' where id = '".$user['id']."'");
		@mail($user['email'], $config['projectName'].': Your new password', "Hi,\n\na new password has been requested for your account on ".$config['projectName'].". Please use the following credentials to log in (the password can be changed in your profile settings).\n".$config['projectURL']."\users\login.html\nE-Mail: ".$user['email']."\nPassword: ".$password, 'Content-Type: text/plain\n');
		$message = createMessage('Your new password has been successfully sent via email.', 'confirm');
	}
	else{
		$message = createMessage(implode('<br />', $errors));
	}
}
?>
<h2>Recover Password</h2>
<?php echo $message;?>
<form action="" method="post">
    <?php
	foreach($fields as $key => $field){
		?>
		<div class="row">
			<label for="setting_<?=$key?>"><?=$field['name']?>:</label>
			<input type="<?=(($field['isPassword']) ? 'password' : 'text')?>" id="setting_<?=$key?>" name="<?=$key?>" value="<?=((isset($_POST[$key])) ? $_POST[$key] : $config[$key])?>" />
		</div>
		<?php
	}
	?>
	<div class="row">
	  <input class="submit" type="submit" name="submit" value="Request new password" /><br /><br />
	</div> 
</form>