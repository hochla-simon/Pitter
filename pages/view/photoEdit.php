<?php


	$site['title'] = 'Edit photo';
	$imageId=$_GET['id'];

	if($imageId != ''){
        $select_sql_string = 'SELECT id, name, description FROM images WHERE id=' . mysql_real_escape_string($imageId);
        $result = $db->query($select_sql_string);
        if (!empty($result)){
            $image = mysql_fetch_array($result);
        }
    }
	if (isset ($_POST["Save"])) {
        if ($_POST["name"] != '') {
            $update_sql_string = 'UPDATE images SET name="' . $_POST["name"] . '",description="' . $_POST["description"] . '" WHERE id="' . $_POST["imageId"] . '" ';
            $db->query($update_sql_string);

            header('Location: ./index.html');
            exit();
        } else {
            http_response_code(500);
            $db->query($delete_sql_string);
            $message  = createMessage("Sorry, there was an error editing your album.");
        }
    }
	print($message);
?>
<h2><?php echo $site['title'];?></h2>

<form action="" method="POST">

    <input type="hidden" name="imageId" id="albumId" value="<?php echo $image['id']; ?>" >

    <div class="row">
        <label for="name">Name :</label>
        <input type="text" name="name" id="name" size="60" value="<?php echo $image['name']; ?>">
    </div>

    <div class="row">
        <label for="description">Description :</label>
        <textarea name="description" id="description" cols="60" rows="5"><?php echo $image['description']; ?></textarea>
    </div>

    <div class="row">
        <input class="cancel" type="button" name="Cancel" value="Cancel" onclick="window.location='./index.html';">
        <input class="submit" type="submit" name="Save" value="Save">
    </div>

</form>