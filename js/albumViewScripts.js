var arrow_right_image = 'arrow_right.png';
var arrow_down_image = 'arrow_down.png';

function closeSubAlbums(parentAlbumId, newImgSrc) {
	$('li[data-id=' + parentAlbumId + ' ] .toggleArrow').attr('src', newImgSrc)
	var albumsToBeClosed = $('ul[data-parentAlbumId=' + parentAlbumId + ' ]');
	albumsToBeClosed.css('display', 'none');
	albumsToBeClosed.each(function() {
		closeSubAlbums($(this).data('id'), newImgSrc);
	});
}

$(document).ready(function() {

	$(".toggleArrow").click(function(index, element) {
		var parentAlbumId = $(this).parent('li').data('id');
		var originalImgSrc = $(this).attr('src');
		var lastSlashIndex = originalImgSrc.lastIndexOf('/') + 1;
		var newImgSrc = '';
		if (originalImgSrc.substring(lastSlashIndex) === arrow_right_image) {
			$('ul[data-parentAlbumId=' + parentAlbumId + ' ]').css('display', '');
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

	$('.albums, .childAlbums').sortable({
            placeholder: "ui-state-highlight",
			connectWith: ".albums, .childAlbums",
			start: function( event, ui ) { /*$(".toggleArrow").click()*/},
            update : function( event, ui ) {
                var albumId = ui.item.attr('data-id');
                var oldParentAlbumId = -1;
                if (ui.sender){
                    oldParentAlbumId = ui.sender.attr('data-parentAlbumId');
                }
                var newParentAlbumId = $(this).attr('data-parentAlbumId');
                if (newParentAlbumId == oldParentAlbumId){
                    $(this).sortable("cancel");
                }
                else {
                    // if old parent Album empty, hide arrow
                    // new parent album, set arrow
                    $.ajax({
                        url : './albumDragDropSave.html', // La ressource ciblée
                        type : 'POST', // Le type de la requête HTTP.
                        data : 'albumId=' + albumId + '&parentAlbumId=' + newParentAlbumId,
                        dataType : 'html'
                    });
                    //$(".toggleArrow").click();
                }
            }
		}
	);
});