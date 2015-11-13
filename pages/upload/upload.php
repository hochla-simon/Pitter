<?php
$target_dir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . 'images'. DIRECTORY_SEPARATOR;
$target_file = $target_dir . basename($_FILES["file"]["name"]);
#echo "will try to move to " . $target_file;
$uploadOk = 1;
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
$imageFileName = pathinfo($target_file,PATHINFO_FILENAME);
// Check if image file is a actual image or fake image

$response_code=200;
if(isset($_POST["albumId"])) {
    $sql = "SELECT parentAlbumId, id, name FROM albums WHERE id='" . mysql_real_escape_string($_POST["albumId"])."'";
    $albums = mysql_fetch_assoc($db->query($sql));
    if (!empty($albums)) {
        $checkFile = getimagesize($_FILES["file"]["tmp_name"]);
        if ($checkFile !== false) {
            $uploadOk = 1;
        } else {
            $response_code=400;
            $uploadOk = 0;
        }
    }else{
        $response_code=401;
        $uploadOk=0;
    }
} else {
    $response_code=404;
    $uploadOk = 0;
}
if($uploadOk == 1){
    // Allow certain file formats
    $imageFileType = strtolower($imageFileType);
    if ($imageFileType === 'jpeg' ){
        $imageFileType = 'jpg';
    }
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $response_code = 415;
        $uploadOk = 0;
    }
// Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
    } else {

        $tmp_id = 'r'.rand();
        $tmpTarget_file_name = $target_dir . $tmp_id . '.' . $imageFileType;
        if (copy($_FILES["file"]["tmp_name"], $tmpTarget_file_name)) {
            $insert_sql_string = 'INSERT INTO images (ownerId,filename, extension, created) VALUES (0,\'' . $imageFileName . '\',\'' . $imageFileType . '\', CURRENT_TIMESTAMP());';


            $db->query($insert_sql_string);
            //$insert_query=$conn->prepare($insert_sql_string);
            //$insert_query->execute($data);
            $last_id = mysql_insert_id();

            $newTarget_file_name = $target_dir . $last_id . '.' . $imageFileType;


            rename($tmpTarget_file_name, $newTarget_file_name);

            $db->query('START TRANSACTION;');
            $db->query('SELECT @maxPositionInAlbum := IFNULL(MAX(positionInAlbum),0) FROM imagesToAlbums WHERE albumId=' . $_POST["albumId"] . ';');
            $db->query('INSERT INTO imagesToAlbums (albumId,imageId,positionInAlbum) VALUES (' . $_POST["albumId"] . ',' . $last_id . ', @maxPositionInAlbum + 1);');
            $db->query('COMMIT;');

            if(!$phpunit['isTest']) {
                header('Content-Type: application/json');
            }
            echo '{"lastId":' . $last_id . '}';

        } else {
            $response_code=500;
            $db->query($delete_sql_string);
            echo "Sorry, there was an error uploading your file.";
        }
    }
}else{
    $response_code=500;
    echo "Sorry, there was an error uploading your file.";
}

if($uploadOk==0){
    http_response_code($response_code);
}

if(!$phpunit['isTest']) {
    die();
}