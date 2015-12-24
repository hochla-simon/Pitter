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
	$data['url'] = $module.'/';
	if($data['file'] != ''){
		$data['url'] .= $data['file'];
	}
	else{
		$data['url'] .= 'index.html';
	}
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

function deliver_image_content($id, $extension, $isTest){

    $path = dirname(__FILE__) . '/../data/images/' . $id . '.' . $extension;

    $extension = strtolower($extension);
    if ($extension == 'jpg') {
        $extension = "jpeg";
    }
    if($isTest) {
        header('Content-Type: image/' . $extension);
    }

    if(isset($_GET["max_size"])){
        if($isTest) {
            /*header("Pragma: cache");*/
            header("Cache-Control: max-age=" . 24 * 60 * 60);
            $time_last_modification = filemtime($path);
            header("Last-Modified: " . date("F d Y H:i:s.", $time_last_modification));
        }
        if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
            &&
            (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $time_last_modification)) {
            if($isTest) {
                // send the last mod time of the file back
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $time_last_modification) . ' GMT',
                    true, 304);
            }
        } else {
            list($width, $height) = getimagesize($path);
            //echo 'path: '.$path;
            $longest_side = 0;
            if ($width > $height) {
                $longest_side = $width;
            } else {
                $longest_side = $height;
            }

            $max_size = $_GET["max_size"];

            if ($max_size >= $longest_side) {
                readfile($path);
            } else {
                $percent = 0;

                $ratio = $longest_side / $max_size;

                $new_width = $width / $ratio;
                $new_height = $height / $ratio;

                //echo 'new width '.$new_width.' new height '.$new_height;

                // Resample

                if (!extension_loaded('imagick')){
                    ini_set('memory_limit', '1000M');
                    $image_p = imagecreatetruecolor($new_width, $new_height);
                    $image = null;
                    if ($extension == 'jpeg') {
                        $image = imagecreatefromjpeg($path);
                    } else {
                        if ($extension == 'gif') {
                            $image = imagecreatefromgif($path);
                        } else {
                            $image = imagecreatefrompng($path);
                        }
                    }
                    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                    if ($extension == 'jpeg') {
                        imagejpeg($image_p, null, 100);
                    } else if ($extension == 'gif') {
                        imagegif($image_p);
                    } else {
                        imagepng($image_p);
                    }
                    imagedestroy($image_p);
                    imagedestroy($image);
                }else{
                    $image = new \Imagick(realpath($path));
                    $image->thumbnailImage($new_width , $new_height , TRUE);
                    echo $image->getImageBlob();
                }
            }
        }
    } else {
        @readfile($path);
    }
}
?>