<?php

include 'config.php';

$result = mysql_query("SELECT COUNT(1) FROM images");
$row = mysql_fetch_array($result);
$total_records = $row[0];
$total_groups = ceil($total_records/$items_per_group);

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
	$(document).ready(function() {
		var track_load = 0; //total loaded record group(s)
		var loading  = false; //to prevents multipal ajax loads
		var total_groups = <?php echo $total_groups; ?>; //total record group(s)
		var album_id = <?php echo(json_encode($albumId)); ?>;

		// /pages/view/autoload_process.php
		$('#photos').load("http://localhost/view/autoloadProcess.html", {'group_no':track_load, 'album_id':album_id}, function() {track_load++;}); //load first group

		$(window).scroll(function() { //detect page scroll

			if($(window).scrollTop() + $(window).height() == $(document).height())  //user scrolled to bottom of the page?
			{

				if(track_load <= total_groups && loading==false) //there's more data to load
				{
					loading = true; //prevent further ajax loading
					$('.animation_image').show(); //show loading image

					//TODO paramter of host should be autoload_process.php
//                        load data from the server using a HTTP POST request
					$.post('http://localhost/view/autoloadProcess.html',{'group_no':track_load, 'album_id':album_id}, function(data){

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

<div id="photos" class="images">
<div class="animation_image" style="display:none" align="center"><img src="../../images/loader.gif"></div>


<!--
<div id="albumsContainer">
	<ul class="albums" data-parentAlbumId="-1">
		<li class="context-menu-one box menu-1" data-id ="2">
			<img class="toggleArrow" style="visibility: " src="http://localhost/Pitter/images/arrow_right.png" alt=""/>
			<img src="http://localhost/Pitter/images/folder.png" alt=""/>
			<a href="?id=2">album2</a>
			<ul class="albums" data-parentAlbumId="2">
				<li class="context-menu-one box menu-1" data-id ="18">
					<img class="toggleArrow" style="visibility: hidden" src="http://localhost/Pitter/images/arrow_right.png" alt=""/>
					<img src="http://localhost/Pitter/images/folder.png" alt=""/>
					<a href="?id=18">Album1</a>
					<ul class="albums" data-parentAlbumId="18">
					</ul>
				</li>
			</ul>
		</li>
		<li class="context-menu-one box menu-1" data-id ="3">
			<img class="toggleArrow" style="visibility: " src="http://localhost/Pitter/images/arrow_right.png" alt=""/>
			<img src="http://localhost/Pitter/images/folder.png" alt=""/>
			<a href="?id=3">Album3</a>
			<ul class="albums" data-parentAlbumId="3">
				<li class="context-menu-one box menu-1" data-id ="9display: none">
					<img class="toggleArrow" style="visibility: hidden" src="http://localhost/Pitter/images/arrow_right.png" alt=""/>
					<img src="http://localhost/Pitter/images/folder.png" alt=""/>
					<a href="?id=9">Album4</a>
					<ul class="albums" data-parentAlbumId="9">
					</ul>
				</li>
			</ul>
		</li>
		<li class="context-menu-one box menu-1" data-id ="4display: ">
			<img class="toggleArrow" style="visibility: hidden" src="http://localhost/Pitter/images/arrow_right.png" alt=""/>
			<img src="http://localhost/Pitter/images/folder.png" alt=""/>
			<a href="?id=4">Album4</a>
			<ul class="albums" data-parentAlbumId="4"></ul>
		</li>
	</ul>
</div>

-->
