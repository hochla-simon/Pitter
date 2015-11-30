<?php
$moduleConfig = array('navigation' => array());
if($currentUser['isAdmin'] == '1'){
	$moduleConfig['navigation'][] = array(
		'position' => 1000,
		'text' => 'Administration'
	);
}
?>