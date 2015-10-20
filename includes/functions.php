<?php
function createMessage($text, $cssClass = 'error'){
	$msg = '<div class="'.$cssClass.'Message">'.(($cssClass == 'error') ? '<b>Error:</b> ' : '').$text.'</div>';
	if($echo)
		echo $msg;
	else
		return $msg;
}
function createSession($data){
	$_SESSION = $data;
}
function destroySession(){
	session_destroy();
}
function addModuleNavigation($module, $data){
	global $config;
	$data['url'] = '/'.$module.'/index.html';
	$newNavigation = array();
	$i = 0;
	for( ; $i < $data['position'] && $i < count($config['navigation']); $i++){
		$newNavigation[] = $config['navigation'][$i];
	}
	$newNavigation[] = $data;
	for( ; $i < count($config['navigation']); $i++){
		$newNavigation[] = $config['navigation'][$i];
	}
	$config['navigation'] = $newNavigation;
}
?>