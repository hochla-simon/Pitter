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
	$data['url'] = $module.'/index.html';
	$newNavigation = array();
	$i = 0;
	for( ; $i < $data['position'] && $i < count($config['navigation']) && $config['navigation'][$i]['position'] < $data['position']; $i++){
		$newNavigation[] = $config['navigation'][$i];
	}
	$newNavigation[] = $data;
	for( ; $i < count($config['navigation']); $i++){
		$newNavigation[] = $config['navigation'][$i];
	}
	$config['navigation'] = $newNavigation;
}
function get_search_cols($keywords, $tables){
         $search = str_replace('*', '%', $keywords);
         if($keywords != $search)
                 $sign = " like ";
         else
                 $sign = " = ";
         if(@preg_match('§^(-)?([0-9]+),([0-9]+)$§i', $search))
                 $search = str_replace(',', '.', $search);

         $parts = array();
         foreach($tables as $table){
                 $result = mysql_query("show columns from ".$table) or die(mysql_error());
                 while($row = mysql_fetch_assoc($result)){
                         $part = '('.((count($tables) > 1) ? $table."." : "").$row['Field'].$sign.((!is_numeric($search)) ? "'" : "").mysql_real_escape_string($search).((!is_numeric($search)) ? "'" : "");
                         if(@preg_match('§(int|float)§i', $row['Type']) and !is_numeric($search))
                                 $part .= " and ".((count($tables) > 1) ? $table."." : "").$row['Field']." != '0'";
                         $part .= ')';
                         $parts[] = $part;
                 }
         }
         return implode(' or ', $parts);
}
function site_handler($url, $url_first_site, $amount, $v, $order, $limit){
         $handler = '<span>Page</span> ';
         if($amount > $limit){
                 $seite = 1;
                 $seiten = 0;
                 $aktuelle = ($v / $limit) + 1;
                 while($amount > $seiten){
                         if($seite == $aktuelle)
                                 $handler .= '<span>'.$seite.'</span> | ';
                         else
                                 $handler .= "<a href=\"".str_replace(array('{v}', '{order}'), array($seiten, $order), (($seiten > 0) ? $url : $url_first_site))."\">".$seite."</a> | ";
                         $seite = $seite + 1;
                         $seiten = $seiten + $limit;
                 }
        }
        else
                 $handler .= '<span>1</span>';
        return $handler;
}
?>