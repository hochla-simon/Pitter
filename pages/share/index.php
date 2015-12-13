<?php

$result = mysql_query("SELECT COUNT(1) FROM images");
$row = mysql_fetch_array($result);
$total_records = $row[0];
$projectUrl =  $config['projectURL'];



$site['title'] = 'Photos';
$site['script'] = '<link rel="stylesheet" href="' . $config['projectURL'] . 'css/jquery-ui.min.css" type="text/css" />
<link rel="stylesheet" href="' . $config['projectURL'] . 'css/jquery-ui.structure.min.css" type="text/css" />
<link rel="stylesheet" href="' . $config['projectURL'] . 'css/jquery-ui.theme.min.css" type="text/css" />
<link rel="stylesheet" href="' . $config['projectURL'] . 'css/jquery.contextMenu.css" type="text/css" />
<link rel="stylesheet" href="' . $config['projectURL'] . 'css/dropzone.css" type="text/css" />
<script type="text/javascript" src="' . $config['projectURL'] . 'js/jquery-ui.min.js"></script>
<script type="text/javascript" src="' . $config['projectURL'] . 'js/jquery.ui.position.js"></script>
<script type="text/javascript" src="' . $config['projectURL'] . 'js/jquery.contextMenu.js"></script>
<script type="text/javascript" src="' . $config['projectURL'] . 'js/albumViewScripts.js"></script>
<script type="text/javascript" src="' . $config['projectURL'] . 'js/dropzone.js"></script>';

$albumId = $_GET['id'];
if (!$albumId) {
    include(dirname(__FILE__) . '/../common/error401.php');
    exit();
}

$sharelink = $_GET['sharelink'];
if(!isset($sharelink)){
    include(dirname(__FILE__) . '/../common/error401.php');
    exit();
}

$sql = "SELECT * FROM `linkToAlbums` WHERE `albumId` = ".$albumId." AND `link` = '".$sharelink."'";
$result = $db->query($sql);
$row = mysql_fetch_array($result);
if(empty($row)){
    include(dirname(__FILE__) . '/../common/error401.php');
    exit();
}


$query_for_album = "SELECT parentAlbumId, id, ownerId, name FROM albums WHERE id='" . mysql_real_escape_string($albumId) . "'";
$album_data = mysql_fetch_array($db->query($query_for_album));


?> '<div id="albumView">

    <script type="text/javascript">
        var track_load = 0; //total loaded record group(s)
        var loading  = false; //to prevents multipal ajax loads
        var album_id = <?php echo(json_encode($albumId)); ?>;
        var projectUrl = <?php echo(json_encode($projectUrl));?>;
        var images_per_group = 0;

        function loadFirstElements() {
            track_load = 0;
            loading  = false;
            var orderingField;
            switch ($("input.field_order_option.checked")[0].value){
                case "filename":
                    orderingField="fileName";
                    break;
                case "upload order":
                    orderingField="position";
                    break;
                case "capture date":
                    orderingField="date";
                    break;
                default :
                    orderingField="position";
                    break;
            }
            var orderingOrder;
            switch ($("input.ordering_option.checked")[0].value){
                case "ascending":
                    orderingOrder="ASC";
                    break;
                case "descending":
                    orderingOrder="DESC";
                    break;
                default :
                    orderingOrder="ASC";
                    break;
            }
            $('#photos').load(projectUrl + "view/autoloadProcess.html", {'sharelink':'<?=$sharelink?>', 'images_per_group':images_per_group, 'group_no':track_load, 'album_id':album_id, 'ordering':orderingOrder,'ord_field':orderingField}, function() {track_load++;}); //load first group
        }
        function countImagesNumberPerPage() {
            var elmnt = document.getElementById("wrapper");
            var height =  $(window).height() - elmnt.scrollHeight;
            var width = elmnt.scrollWidth;
            var imageSize = 160;
            var imagesInRow = Math.floor(width / imageSize);
            var imagesInColumn = Math.ceil(height / imageSize) + 1;
            var imagesToLoad = imagesInRow * imagesInColumn;
            return imagesToLoad;
        }
        $(document).ready(function() {
            var total_records = <?php echo $total_records; ?>;
            images_per_group = countImagesNumberPerPage();
            var total_groups = total_records / images_per_group;
            loadFirstElements();
            $(window).scroll(function() { //detect page scroll

                if($(window).scrollTop() + $(window).height() == $(document).height())  //user scrolled to bottom of the page?
                {

                    if(track_load <= total_groups && loading==false) //there's more data to load
                    {
                        loading = true; //prevent further ajax loading
                        $('.animation_image').show(); //show loading image

                        //TODO paramter of host should be autoload_process.php
//                        load data from the server using a HTTP POST request

                        var orderingField;
                        switch ($("input.field_order_option.checked")[0].value){
                            case "filename":
                                orderingField="fileName";
                                break;
                            case "upload order":
                                orderingField="position";
                                break;
                            case "capture date":
                                orderingField="date";
                                break;
                            default :
                                orderingField="position";
                                break;
                        }
                        var orderingOrder;
                        switch ($("input.ordering_option.checked")[0].value){
                            case "ascending":
                                orderingOrder="ASC";
                                break;
                            case "descending":
                                orderingOrder="DESC";
                                break;
                            default :
                                orderingOrder="ASC";
                                break;
                        }


                        $.post(projectUrl + "view/autoloadProcess.html",{'sharelink':'<?=$sharelink?>', 'images_per_group':images_per_group, 'group_no':track_load, 'album_id':album_id, 'ordering':orderingOrder,'ord_field':orderingField}, function(data){

                            $("#photos").append(data); //append received data into the element

                            //hide loading image
                            $('.animation_image').hide(); //hide loading image once data is received

                            track_load++; //loaded group increment
                            loading = false;

                        }).fail(function(xhr, ajaxOptions, thrownError) { //any errors?
//                            getOutput(track_load);

                            alert(thrownError); //alert with HTTP error
                            $('.animation_image').hide(); //hide loading image
                            loading = false;

                        });
                    }
                }
            });
        });
    </script>


    <div class="ordering_menu_container">
        <form class="view_menu">
            <a class="view_menu_option" href=<?php echo $config['projectURL'] ?>slideshow/index.html?album=<?php echo $albumId?>>Slideshow</a>
        </form>
        <form class="field_order_menu">
            <input class="field_order_option checked" type="button" value="upload order">
            <input class="field_order_option" type="button" value="filename">
            <input class="field_order_option" type="button" value="capture date">
        </form>
        <form class="ordering_menu">
            <input class="ordering_option checked" type="button" value="ascending">
            <input class="ordering_option" type="button" value="descending">
        </form>
    </div>

    <script>
        $(function() {
            $("input.field_order_option").on('click', function() {
                // ajax process
                $("input.field_order_option").removeClass("checked");
                $(this).addClass("checked");
                loadFirstElements();
            });
            $("input.ordering_option").on('click', function() {
                // ajax process
                $("input.ordering_option").removeClass("checked");
                $(this).addClass("checked");
                loadFirstElements();
            });
        });
    </script>
    <div id="photos" class="images" data-albumId="<?php echo $albumId ?>"/><div class="animation_image" style="display:none" align="center"><img src="<?php echo $config['projectURL'];?>images/loader.gif"></div>
</div>
</div>

