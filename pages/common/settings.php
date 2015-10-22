<?php
$site['title'] = 'Settings';
$site['script'] = '';

$fields = array(
	'databaseHost' => array('name' => 'Database Host'),
	'databaseUser' => array('name' => 'Database User'),
	'databasePassword' => array('name' => 'Database Password', 'isPassword' => true),
	'databaseName' => array('name' => 'Database Name'),
	'projectName' => array('name' => 'Project Name'),
	'slogan' => array('name' => 'Project Slogan', 'isHTML' => true),
	'copyright' => array('name' => 'Copyright', 'isHTML' => true),
	'homeContent' => array('name' => 'Home page', 'isHTML' => true)
);

$message = '';
if(isset($_POST['submit'])){
	$errors = array();
	$config['users'] = array();
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
		$db = new Database();
		$config = array_merge($config, array('db_name' => $_POST['db_name'], 'db_host' => $_POST['db_host'], 'db_port' => $_POST['db_port'], 'db_user' => $_POST['db_user'], 'db_pass' => $_POST['db_pass']));
		if(!$db->connect($_POST['databaseHost'], $_POST['databaseUser'], $_POST['databasePassword'], $_POST['databaseName'])){
			$errors[] = 'Could not connect to database.';
		}
	}
	if(count($errors) == 0){
		$open = fopen(dirname(__FILE__).'/config.txt', 'w+');
		$config['installed'] = true;
		fwrite($open, json_encode($config));
		fclose($open);
		$message = createMessage('Changes successfully saved.', 'confirm');
	}
	else{
		$message = createMessage(implode('<br />', $errors));
	}
}
?>
<h1><?php echo $site['title'];?></h1>
<?=$message?>
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
  <input type="submit" name="submit" value="Save Settings" />
 </div>
</form>