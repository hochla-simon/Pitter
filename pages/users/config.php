<?php
$moduleConfig = array('navigation' => array());
if($currentUser['id'] != ''){
	$moduleConfig['navigation'][] = array(
		'position' => 900,
		'text' => 'My Profile',
		'file' => 'profile.html'
	);
	$moduleConfig['navigation'][] = array(
		'position' => 5000,
		'text' => 'Logout',
		'file' => 'logout.html'
	);
}
else{
	$moduleConfig['navigation'][] = array(
		'position' => 900,
		'text' => 'Login',
		'file' => 'login.html'
	);
	$moduleConfig['navigation'][] = array(
		'position' => 950,
		'text' => 'Register',
		'file' => 'register.html'
	);
}
?>