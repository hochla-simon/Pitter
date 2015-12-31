<?php
/**
 * Created by PhpStorm.
 * User: Simon
 * Date: 30. 12. 2015
 * Time: 0:36
 */
class TestLinkBasedSharing extends PHPUnit_Framework_TestCase {

    public function TestLinkGenerator()
    {
        $projectUrl = $config['projectURL'];

        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__) . '/../index.php');
        $albumId = '1';

        $_GET = array(
            'id' => $albumId
        );
        include(dirname(__FILE__) . '/../pages/share/linkGenerator.php');

        if (empty($row)) {
            $shareLink = $link;
        } else {
            $shareLink = $row['link'];
        }

        $sql = "SELECT `link` FROM `linkToAlbums` WHERE  `albumId` = '" . mysql_real_escape_string($albumId) . "'";
        $result = $db->query($sql);
        $row = mysql_fetch_array($result);
        $this->assertEquals($shareLink, $row[0]);
        $this->assertEquals(1, mysql_num_rows($result));
    }
    public function TestSearchPageWithCorrectData() {
        $projectUrl = $config['projectURL'];

        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__) . '/../index.php');
        $albumId = '1';

        $_GET = array(
            'id' => $albumId
        );
        include(dirname(__FILE__) . '/../pages/share/linkGenerator.php');

        if (empty($row)) {
            $shareLink = $link;
        } else {
            $shareLink = $row['link'];
        }

        $_GET = array(
            'id' => $albumId,
            'sharelink' => $shareLink
        );
        include(dirname(_FILE_).'/../pages/share/index.php');
        $this->assertEquals(1, mysql_num_rows($result));
    }

    public function TestSearchPageWithEmptyShareLink() {
        $projectUrl = $config['projectURL'];

        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__) . '/../index.php');
        $albumId = '1';

        $_GET = array(
            'id' => $albumId
        );
        include(dirname(__FILE__) . '/../pages/share/linkGenerator.php');

        $emptyShareLink = "";

        $_GET = array(
            'id' => $albumId,
            'sharelink' => $emptyShareLink
        );
        include(dirname(_FILE_).'/../pages/share/index.php');
        $this->assertEquals(0, mysql_num_rows($result));
    }

    public function TestSearchPageWithWrongShareLink() {
        $projectUrl = $config['projectURL'];

        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__) . '/../index.php');
        $albumId = '1';

        $_GET = array(
            'id' => $albumId
        );
        include(dirname(__FILE__) . '/../pages/share/linkGenerator.php');

        $newAlbumId = '2';

        $_GET = array(
            'id' => $newAlbumId
        );
        include(dirname(__FILE__) . '/../pages/share/linkGenerator.php');

        if (empty($row)) {
            $newShareLink = $link;
        } else {
            $newShareLink = $row['link'];
        }

        $_GET = array(
            'id' => $albumId,
            'sharelink' => $newShareLink
        );
        include(dirname(_FILE_).'/../pages/share/index.php');
        $this->assertEquals(0, mysql_num_rows($result));
    }

    public function TestSearchPageWithWrongAlbumId() {
        $projectUrl = $config['projectURL'];

        $_SESSION['id'] = 1;
        $phpunit = array(
            'isTest' => true
        );
        include(dirname(__FILE__) . '/../index.php');
        $albumId = '1';

        $_GET = array(
            'id' => $albumId
        );
        include(dirname(__FILE__) . '/../pages/share/linkGenerator.php');

        if (empty($row)) {
            $shareLink = $link;
        } else {
            $shareLink = $row['link'];
        }

        $wrongAlbumId = '-2';

        $_GET = array(
            'id' => $wrongAlbumId,
            'sharelink' => $shareLink
        );
        include(dirname(_FILE_).'/../pages/share/index.php');
        $this->assertEquals(0, mysql_num_rows($result));
    }
}
?>
