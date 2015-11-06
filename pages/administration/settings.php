<?php
$site['title'] = (($config['installed']) ? 'Settings' : 'Installation');
$site['script'] = '<script type="text/javascript" src="'.$config['projectURL'].'js/tinymce/tinymce.min.js"></script>
	<script type="text/javascript">
        tinymce.init({
            selector: "#setting_slogan,#setting_copyright,#setting_homeContent"
        });
    </script>';

$fields = array(
	'databaseHost' => array('name' => 'Database Host'),
	'databaseUser' => array('name' => 'Database User'),
	'databasePassword' => array('name' => 'Database Password', 'isPassword' => true),
	'databaseName' => array('name' => 'Database Name'),
	'projectName' => array('name' => 'Project Name'),
	'projectURL' => array('name' => 'Project URL'),
	'slogan' => array('name' => 'Project Slogan', 'isHTML' => true),
	'copyright' => array('name' => 'Copyright', 'isHTML' => true),
	'homeContent' => array('name' => 'Home page', 'isHTML' => true)
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
		$testDB = new Database();
		unset($_POST['submit']);

		if(!$testDB->connect($_POST['databaseHost'], $_POST['databaseUser'], $_POST['databasePassword'], $_POST['databaseName'])){
			$errors[] = 'Could not connect to database.';
		}
	}
	if(count($errors) == 0 and !$config['installed']){
		$db = new Database();
		!$db->connect($_POST['databaseHost'], $_POST['databaseUser'], $_POST['databasePassword'], $_POST['databaseName']);
		if(!$db->install()){
			$errors[] = 'Database error: '.$db->getErrorMessage();
		}
	}
	if(count($errors) == 0){
		$newConfig = array_merge($config, $_POST);
        unset($newConfig['navigation']);
        unset($newConfig['modules']);
        if($newConfig['databaseType'] == ''){
            $newConfig['databaseType'] = 'mysql';
        }
        $newConfig['installed'] = true;
		$open = fopen(dirname(__FILE__).'/../../data/configuration/config.txt', 'w+');
		fwrite($open, json_encode($newConfig));
		fclose($open);
		$db = $testDB;
		if($config['installed']){
			$message = createMessage('Changes successfully saved.', 'confirm');
		}
		else{
			$message = createMessage('Installation successful. You will be redirected...', 'confirm');
			echo '<meta http-equiv="refresh" content="2; url='.$newConfig['projectURL'].'">';
		}
		include(dirname(__FILE__).'/../../includes/config.php');
	}
	else{
		$message = createMessage(implode('<br />', $errors));
	}
}
?>
<h1><?php echo $site['title'];?></h1>
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
 <?php
 if(!$config['installed']){
	?>
	 <script type="text/javascript">
	  $(document).ready(function(){
		  $('#setting_projectURL').val(location.protocol + '//' + (location.host + '/' + location.pathname).replace(/\/\//, '/'));
	  });
	 </script>
	<?php
 }
 ?>
</form>