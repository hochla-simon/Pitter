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
		$testDB = new Database();
		unset($_POST['submit']);
		if(!$testDB->connect($_POST['databaseHost'], $_POST['databaseUser'], $_POST['databasePassword'], $_POST['databaseName'])){
			$errors[] = 'Could not connect to database.';
		}
	}
	if(count($errors) == 0){
		$newConfig = array_merge($config, $_POST);
		unset($newConfig['navigation']);
		$newConfig['installed'] = true;
		$open = fopen(dirname(__FILE__).'/../../data/configuration/config.txt', 'w+');
		fwrite($open, json_encode($config));
		fclose($open);
		$db = $testDB;
		$message = createMessage('Changes successfully saved.', 'confirm');
	}
	else{
		$message = createMessage(implode('<br />', $errors));
	}
}
?>
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
  <input type="submit" name="submit" value="Save Settings" />
 </div>
</form>