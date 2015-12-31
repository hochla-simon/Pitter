<?php
if($currentUser['id'] == ''):
    $_POST['redirect'] = $_SERVER['REQUEST_URI'];
    include(dirname(__FILE__).'/../users/login.php');
else:

    $site['title'] = 'Edit photo';
    $imageId=$_GET['id'];
    $accessDenied = false;

    if($imageId != ''){
        $select_sql_string = "SELECT id, ownerId, name, filename, extension, created, description FROM images WHERE id=" . mysql_real_escape_string($imageId);
        $result = $db->query($select_sql_string);
        if (!empty($result)){
            $image = mysql_fetch_array($result);
            if($image['ownerId']!=$currentUser['id']) {
                $denied = true;
                if(!$phpunit['isTest']) {
                    include(dirname(__FILE__) . '/../common/error401.php');
                    exit();
                }
                $accessDenied = true;
            }
        }

    }
    if($denied){
        if(!$phpunit['isTest']) {
            include(dirname(__FILE__) . '/../common/error401.php');
            exit();
        }
    }
	if (isset ($_POST["Save"])) {
        $update_sql_string = 'UPDATE images SET name="' . $_POST["name"] . '",description="' . $_POST["description"] . '" WHERE id="' . $_POST["imageId"] . '" ';
        $db->query($update_sql_string);
        if (!$phpunit['isTest']) {
            header('Location: ./index.html?id='.$_GET['albumId']);
            exit();
        }
    }
    if (!$phpunit['isTest']) {
        print($message);
        ?>
        <h2><?php echo $site['title']; ?></h2>

        <form action="" method="POST">

            <input type="hidden" name="imageId" id="imageId" value="<?php echo $image['id']; ?>">

            <div class="row">
                <label for="name">Name :</label>
                <input type="text" name="name" id="name" size="60" value="<?php echo $image['name']; ?>">
            </div>

            <div class="row">
                <label for="description">Description :</label>
                <textarea name="description" id="description" cols="60"
                          rows="5"><?php echo $image['description']; ?></textarea>
            </div>

            <div class="row">
                <input class="cancel" type="button" name="Cancel" value="Cancel"
                       onclick="window.location='./index.html?id=<?php echo $_GET['albumId'];?>';">
                <input class="submit" type="submit" name="Save" value="Save">
            </div>
        </form>
        <?php
    }
endif;
?>