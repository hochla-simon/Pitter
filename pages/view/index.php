<?php

include 'config.php';

$result = mysql_query("SELECT COUNT(1) FROM images");
$row = mysql_fetch_array($result);
$total_records = $row[0];
$total_groups = ceil($total_records/$items_per_group);
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

function orderAlbums($id, &$children, $albumsToOrder) {
	foreach($albumsToOrder as $albumId => $album) {
		if ($album['parentAlbumId'] == $id) {
			$children[$albumId] = $album;
			orderAlbums($albumId, $children[$albumId]['childAlbums'], $albumsToOrder);
		}
	}
}

function createAlbums ($albums, $subNumber, $parentId, $activeAlbumId) {
	global $config;
	$display = 'none';
	if ($subNumber == 0) {
		$display = '';
		$albumListClass = 'albums';
	} else {
		$albumListClass = 'childAlbums';
	}

	echo '<ul class="' . $albumListClass . '" data-albumId="' . $parentId . '" style="display: ' . $display . '">';
	foreach ($albums as $albumId => $album) {
		$visibility = '';
		if (empty($album['childAlbums'])) {
			$visibility = 'hidden';
		}
		$albumClass = '';
		if ($albumId == $activeAlbumId) {
			$albumClass = 'active';
		}
		echo '<li class="context-menu-one box menu-1" data-id ="' . $albumId . '" data-path="' . $config['projectURL'] . '">
			<img class="toggleArrow" style="visibility: ' . $visibility . '" src="' . $config['projectURL'] . 'images/arrow_right.png" alt=""/>
			<a class="' . $albumClass . '" href="' . $config['projectURL'] . 'view/index.html?id=' . $albumId .'">
				<img src="' . $config['projectURL'] . 'images/folder.png" alt=""/>
				<span>' . $album[name] . '</span>
			</a>';

		createAlbums($album['childAlbums'], $subNumber + 1, $albumId, $activeAlbumId);

		echo '</li>';
	}
	echo '</ul>';
}

$albumId = $_GET['id'];
if (!$albumId) {
	$albumId = '1';
}

$sql = "SELECT parentAlbumId, id, name FROM albums ORDER BY name ASC";
$albums = $db->query($sql);

if (!empty($albums)) {

	$albumObjects = array();
	while($row = mysql_fetch_array($albums)) {
		$albumObject = array(
			'name' => $row['name'],
			'parentAlbumId' => $row['parentAlbumId'],
			'childAlbums' => array()
		);
		$albumObjects[$row['id']] = $albumObject;
		if ($row['id'] == $albumId) {
			$albumName = $row['name'];
		}
	}

	$orderedAlbumObjects = array();

	orderAlbums('-1', $orderedAlbumObjects, $albumObjects);

	echo '<div id="albumsContainer">';

	createAlbums($orderedAlbumObjects, 0, '-1', $albumId);

	echo '</div>';
}

echo '<div id="albumView">';

echo '<div id="upload">

<form action="'.$config['projectURL'].'upload/upload.html"
      class="dropzone"
      id="myDropzone">';
	if($albumId) echo '<input type="hidden" name="albumId" value="'.$albumId.'" />';
	else echo '<input type="hidden" name="albumId" value="1" />';
echo '</form>';

echo '<script>

        Dropzone.options.myDropzone = {
            dictInvalidFileType : "only jpg, jpeg, png and gif are accepted",
            acceptedFiles: "image/jpeg,image/png,image/gif",
            init: function() {
                this.on("error", function (file, errorMessage, XMLHttpRequestMessage) {
                	debugger;
                	if (XMLHttpRequestMessage===undefined){

                	}else{
                		$($(file.previewElement).find("div.dz-error-message")[0]).find("span").html("Error uploading");
                	}
                });
                this.on("success", function (file, response) {
                    if(file.accepted){
                        htmlNewTag = \'<div class="thumbnail"><span class="center_img"></span><a href="photoView.html?id=\'+response.lastId+\'"><img src="image.html?id=\'+response.lastId+\'&max_size=100"></img></a></div>\';
                        $("div#photos").append(htmlNewTag);
                        this.removeFile(file);
                    }
                });
            }
        }

</script>
</div>';
?>

<script type="text/javascript">
    var track_load = 0; //total loaded record group(s)
    var loading  = false; //to prevents multipal ajax loads
    var total_groups = <?php echo $total_groups; ?>; //total record group(s)
    var album_id = <?php echo(json_encode($albumId)); ?>;
    var projectUrl = <?php echo(json_encode($projectUrl));?>;

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
        $('#photos').load(projectUrl + "view/autoloadProcess.html", {'group_no':track_load, 'album_id':album_id, 'ordering':orderingOrder,'ord_field':orderingField}, function() {track_load++;}); //load first group
    }
	$(document).ready(function() {
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


                    $.post(projectUrl + "view/autoloadProcess.html",{'group_no':track_load, 'album_id':album_id, 'ordering':orderingOrder,'ord_field':orderingField}, function(data){

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
<div id="photos" class="images"/><div class="animation_image" style="display:none" align="center"><img src="<?php echo $config['projectURL'];?>images/loader.gif"></div>
</div>
</div>