
function dqis_image_manager() {
	query = 'field=imagemanager';
	
	image_manager(query);
};

var image_upload_uniq_id = 0;

function upload_image(context) {
	field = context.closest('.image').find('.iu_image');
	thumb = context.closest('.image').find('.iu_thumb');
	
	if(!field.attr('id')){
		field.attr('id', 'iu_image_' + image_upload_uniq_id);
	}
	
	if(!thumb.attr('id')){
		thumb.attr('id', 'iu_thumb_' + image_upload_uniq_id);
	}
	
	query = '&field=' + field.attr('id') + '&thumb=' + thumb.attr('id');
	
	image_manager(query);
	
	image_upload_uniq_id++;
};


function upload_images(field,thumb,rows) {
	query = '&field=' + field + '&thumb=' + thumb;
	
	if(typeof rows == 'integer'){
		query += '&rows=' + rows;
	}
	
	image_manager(query);
};

function clear_image(context){
	context.find('.iu_image').val('');
	context.find('.iu_thumb').attr('src', no_image);
}

function addSingleImage(imageName, field, thumb) {
	field = $('#' + field);
	thumb = $('#' + thumb);
	
	image_size = '';
	
	if(thumb.width() && thumb.height()){
		image_size = '&image_width=' + thumb.width() + '&image_height=' + thumb.height();
	}
	
	$.ajax({
		url: image_manager_url + '/image&image=' + encodeURIComponent(imageName) + image_size,
		dataType: 'text',
		success: function(text) {
			thumb.attr('src', text);
			field.val(imageName);
			
			console.log('thumb');
			console.dir(thumb);
			console.log(text);
			console.dir(field);
			console.log(imageName);
			console.log('adding single image');
	
		}
	});
};

function image_manager(query) {
	query = query || '';
	
	$('#im_dialog').remove();
	$('#content').prepend('<div id="im_dialog"><iframe src="' + image_manager_url + query + '" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#im_dialog').dialog({
		title: 'File Manager',
		close: function (event, ui) {
		},
		bgiframe: false,
		width: 800,
		height: 620,
		resizable: false,
		modal: false
	});
};