var arrow_right_image = 'arrow_right.png';
var arrow_down_image = 'arrow_down.png';

function closeSubAlbums(parentAlbumId, newImgSrc) {
	$('li[data-id=' + parentAlbumId + ' ] .toggleArrow').attr('src', newImgSrc)
	var albumsToBeClosed = $('li[data-parentAlbumId=' + parentAlbumId + ' ]');
	albumsToBeClosed.css('display', 'none');
	albumsToBeClosed.each(function() {
		closeSubAlbums($(this).data('id'), newImgSrc);
	});
}

$(document).ready(function() {

	$(".albums").menu();

	$(".toggleArrow").click(function(index, element) {
		var parentAlbumId = $(this).parent('li').data('id');
		var originalImgSrc = $(this).attr('src');
		var lastSlashIndex = originalImgSrc.lastIndexOf('/') + 1;
		var newImgSrc = '';
		if (originalImgSrc.substring(lastSlashIndex) === arrow_right_image) {
			$('li[data-parentAlbumId=' + parentAlbumId + ' ]').css('display', '');
			newImgSrc = originalImgSrc.substring(0, lastSlashIndex) + arrow_down_image;
			$(this).attr('src', newImgSrc);
		} else {
			newImgSrc = originalImgSrc.substring(0, lastSlashIndex) + arrow_right_image;
			closeSubAlbums(parentAlbumId, newImgSrc);
		}
	});
	$(function(){
		$.contextMenu({
			selector: '.context-menu-one',
			callback: function(key, opt) {
				var albumId = opt.$trigger.attr("data-id");
				if (key === 'new') {
					window.open('./albumCreate.html?parentId=' + albumId, '_self');
				} else if (key === 'edit') {
					window.open('./albumEdit.html?id=' + albumId, '_self');
				} else if (key === 'delete') {
					window.open('./albumDelete.html?id=' + albumId, '_self');
				} else if (key === 'copy') {
					window.open('./albumCopy.html?id=' + albumId, '_self');
				} else if (key === 'move') {
					window.open('./albumMove.html?id=' + albumId, '_self');
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
	});

	$('.albums').sortable({
            placeholder: "ui-state-highlight",
			connectWith: ".albums",
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
                        url : './albumDragDropSave.html', // La ressource cibl�e
                        type : 'POST', // Le type de la requ�te HTTP.
                        data : 'albumId=' + albumId + '&parentAlbumId=' + newParentAlbumId,
                        dataType : 'html'
                    });
                    //$(".toggleArrow").click();
                }
            }
		}
	);
});