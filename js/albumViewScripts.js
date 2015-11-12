var arrow_right_image = 'arrow_right.png';
var arrow_down_image = 'arrow_down.png';

function closeSubAlbums(parentAlbumId, newImgSrc) {
	$('li[data-id=' + parentAlbumId + ' ] .toggleArrow').attr('src', newImgSrc)
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
    var oldList;
    $('.childAlbums').sortable({
            placeholder: "ui-state-highlight",
            connectWith: ".childAlbums",
            start: function( event, ui ) {
                $(".childAlbums").css('display', '');
                oldList = ui.item.parent();
            },
            stop : function( event, ui ) {
                var albumId = ui.item.attr('data-id');
				var newParentAlbumId = $(ui.item).closest('ul').attr('data-albumId');
				sortSubAlbums();

                    // if old parent Album empty, hide arrow
                    // new parent album, set arrow
                    $.ajax({
                        url : './albumDragDropSave.html', // La ressource cibl�e
                        type : 'POST', // Le type de la requ�te HTTP.
                        data : 'albumId=' + albumId + '&parentAlbumId=' + newParentAlbumId,
                        dataType : 'html'
                    });
            }
        }
    );
    $( '.albums, .childAlbums' ).disableSelection();
	$(".toggleArrow:first").click();
});