<?php
function createMessage($text, $cssClass = 'error'){
	$msg = '<div class="'.$cssClass.'Message">'.(($cssClass == 'error') ? '<b>Error:</b> ' : '').$text.'</div>';
	if($echo)
		echo $msg;
	else
		return $msg;
}
?>