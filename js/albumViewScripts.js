var arrow_right_image = 'arrow_right.png';
var arrow_down_image = 'arrow_down.png';

function closeSubAlbums(parentAlbumId, newImgSrc) {
	$('li[data-id=' + parentAlbumId + ' ] .toggleArrow').attr('src', newImgSrc);
	var albumListToBeClosed = $('ul[data-albumId=' + parentAlbumId + ' ]');
	albumListToBeClosed.css('display', 'none');
	var childAlbumsToBeClosed = albumListToBeClosed.children();
	childAlbumsToBeClosed.each(function(index, element) {
		closeSubAlbums($(element).data('id'), newImgSrc);
	});
}

function sortSubAlbums(){
	var albums = $('.albums ul');
	for(var i = 0; i < albums.length; i++){
		var subAlbums = $('>li', albums[i]);
		for(var j = 0; j < subAlbums.length; j++){
			for(var k = 0; k < subAlbums.length - 1; k++){
				if($('span', subAlbums[k]).text() > $('span', subAlbums[k + 1]).text()){
					var tmp = subAlbums[k];
					subAlbums[k] = subAlbums[k + 1];
					subAlbums[k + 1] = tmp;
				}
			}
		}
		for(var j = 0; j < subAlbums.length; j++){
			$(albums[i]).append(subAlbums[j]);
		}
	}
}

function addFirstChildAlbum(parentAlbumId){
    var arrow = $('li[data-id=' + parentAlbumId + ' ] .toggleArrow:first');
    var originalImgSrc = arrow.attr('src').substring(0, arrow.attr('src').lastIndexOf('/') + 1);

    arrow.attr('src', originalImgSrc + arrow_down_image);
    arrow.css('visibility', '');
}

function deleteLastChildAlbum(parentAlbumId){
    if ($('ul[data-albumId=' + parentAlbumId + ' ] li').length === 0){
        var arrow = $('li[data-id=' + parentAlbumId + ' ] .toggleArrow:first');
        arrow.css('visibility', 'hidden');
    }
}

$(document).ready(function() {

	$(".toggleArrow").click(function(index, element) {
		var parentAlbumId = $(this).parent('li').data('id');
		var originalImgSrc = $(this).attr('src');
		var lastSlashIndex = originalImgSrc.lastIndexOf('/') + 1;
		var newImgSrc = '';
		if (originalImgSrc.substring(lastSlashIndex) === arrow_right_image) {
			$('ul[data-albumId=' + parentAlbumId + ' ]').css('display', '');
			newImgSrc = originalImgSrc.substring(0, lastSlashIndex) + arrow_down_image;
			$(this).attr('src', newImgSrc);
		} else {
			newImgSrc = originalImgSrc.substring(0, lastSlashIndex) + arrow_right_image;
			closeSubAlbums(parentAlbumId, newImgSrc);
		}
	});

	$.contextMenu({
		selector: '.context-menu-one',
		callback: function(key, opt) {
			var albumId = opt.$trigger.attr("data-id");
			var path = opt.$trigger.attr("data-path");
			if (key === 'new') {
				window.open(path + 'view/albumCreate.html?parentId=' + albumId, '_self');
			} else if (key === 'edit') {
				window.open(path + 'view/albumEdit.html?id=' + albumId, '_self');
			} else if (key === 'delete') {
				window.open(path + 'view/albumDelete.html?id=' + albumId, '_self');
			} else if (key === 'copy') {
				window.open(path + 'view/albumCopy.html?id=' + albumId, '_self');
			} else if (key === 'move') {
				window.open(path + 'view/albumMove.html?id=' + albumId, '_self');
			}
		},
		items: {
			'new': {name: 'Add new album'},
			'edit': {name: 'Edit album'},
			'delete': {name: 'Delete album'},
			'copy': {name: 'Copy to...'},
			'move': {name: 'Move to...'}
		}
	});

	$.contextMenu({
		selector: '.thumbnail',
		callback: function(key, opt) {
			var photoId = opt.$trigger.parent().attr("id").substring(6);
			var albumId = opt.$trigger.parent().parent().attr("data-albumid");
			if (key === 'edit') {
				window.open('photoEdit.html?id=' + photoId + '&albumId=' + albumId, '_self');
			} else if (key === 'delete') {
				window.open('photoDelete.html?id=' + photoId + '&albumId=' + albumId, '_self');
			} else if (key === 'copy') {
				window.open('photoCopy.html?id=' + photoId + '&albumId=' + albumId, '_self');
			} else if (key === 'move') {
				window.open('photoMoveRightClick.html?id=' + photoId + '&albumId=' + albumId, '_self');
			}
		},
		items: {
			'edit': {name: 'Edit photo'},
			'delete': {name: 'Delete photo'},
			'copy': {name: 'Copy to...'},
			'move': {name: 'Move to...'}
		}
	});

    var oldParentAlbumId;
    $('.childAlbums').sortable({
            placeholder: "ui-state-highlight",
            connectWith: ".childAlbums",
            start: function( event, ui ) {
                $(".childAlbums").css('display', '');
                $('img[style="visibility: "].toggleArrow').each(function(){
                    var originalImgSrc = $(this).attr('src');
                    var lastSlashIndex = originalImgSrc.lastIndexOf('/') + 1;
                    var newImgSrc = originalImgSrc.substring(0, lastSlashIndex) + arrow_down_image;
                    $(this).attr('src', newImgSrc);
                });
                oldParentAlbumId = $(ui.item).closest('ul').attr('data-albumId');
            },
            stop : function( event, ui ) {
                var albumId = ui.item.attr('data-id');
				var newParentAlbumId = $(ui.item).closest('ul').attr('data-albumId');
				sortSubAlbums();
                addFirstChildAlbum(newParentAlbumId);
                deleteLastChildAlbum(oldParentAlbumId);
                    $.ajax({
                        url : './albumDragDropSave.html',
                        type : 'POST',
                        data : 'albumId=' + albumId + '&parentAlbumId=' + newParentAlbumId,
                        dataType : 'html'
                    });
            }
        }
    );
    $( '.albums, .childAlbums' ).disableSelection();
    sortSubAlbums();

	$(".albums .active").parent('li').parents('li').each(function(index, element) {
		$(element).children('.toggleArrow').click();
	});

	$("#photos").sortable({
		placeholder: 'ui-state-highlight',
		forcePlaceholderSize: true,
		update: function() {
			var order = $('#photos').sortable('serialize');
			$.ajax({
				url : './photoOrderSave.html',
				type : 'POST',
				data : order  + '&parentAlbumId=' + $(this).attr('data-albumId'),
				dataType : 'html'
			});
		}
	});

	$(".droppableAlbum").droppable({
		accept: ".draggablePhoto",
		hoverClass: "ui-state-hover",
		drop: function(event, ui) {
			var path = $(this).closest("li").attr("data-path");
			var imageId = $(ui.draggable).attr("data-id");
			var albumId = $(".droppableAlbum.active").closest("li").attr("data-id");
			var newAlbumId = $(this).closest("li").attr("data-id");
			$.ajax({
				url : path + 'view/photoMove.html',
				type : 'POST',
				data : 'path=' + path + '&imageId=' + imageId + '&albumId=' + albumId + '&newAlbumId=' + newAlbumId,
				dataType : 'html'
			});
			$(ui.helper).remove();
			$(ui.draggable).remove();
		}
	})
});