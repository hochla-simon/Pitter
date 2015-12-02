<?php
$site['title'] = 'Login';

$fields = array(
	'email' => array('name' => 'E-Mail Address'),
	'password' => array('name' => 'Password', 'isPassword' => true),
);

$message = '';
if(isset($_POST['login'])){
	$errors = array();
	foreach($_POST as $key => $val){
		if($key != 'redirect' and $fields[$key] == ''){
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
		if($user['id'] == '' or $user['password'] != crypt($_POST['password'], $user['password'])){
			$errors[] = 'No user having these credentials could be found.';
		}
		else if($user['enabled'] == 0){
			$errors[] = 'Your account has not been activated by the administrator yet.';
		}
	}
	if(count($errors) == 0){
		$_SESSION['id'] = $user['id'];
		if($_POST['redirect'] == ''){
			$_POST['redirect'] = $config['projectURL'].'users/profile.html';
		}
		if(!$phpunit['isTest']) {
			header('Location: '.$_POST['redirect']);
			die();
		}
	}
	else{
		$message = createMessage(implode('<br />', $errors));
	}
}
?>
<h2>Login</h2>
<?php echo $message;?>
<form action="<?php echo $config['projectURL'];?>users/login.html" method="post">
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
	  <input type="hidden" name="redirect" value="<?=$_POST['redirect']?>" />
	  <input class="submit" type="submit" name="login" value="Log in" /><br /><br />
	  <a href="<?php echo $config['projectURL'];?>users/recoverPassword.html">Password forgotten?</a>
	</div> 
</form>