
function dqis_image_manager() {
	$('#dialog').remove(); 
	$('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/elmanager&field=imagemanager" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
	
	$('#dialog').dialog({
		title: 'Betty Image Manager',
		close: function (event, ui) {         
			},
		bgiframe: false,
		width: 800,
		height: 620,
		resizable: false,
		modal: false
	});
};
 
function el_uploadSingle(field,thumb,rows) {
    if (arguments.length < 3) {
        rows = -1
    }
   $('#dialog').remove();  
   $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/elmanager&field=' + field + '&thumb=' + thumb + (rows>=0?'&rows=' + rows:'') + '" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
   
   $('#dialog').dialog({
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

function addSingleImage(imageName, field, thumb) {
   field = $('#' + field);
   thumb = $('#' + thumb);
   
   image_size = '';
   
   if(thumb.width() && thumb.height()){
      image_size = '&image_width=' + thumb.width() + '&image_height=' + thumb.height();
   }
   
   $.ajax({
     url: 'index.php?route=common/filemanager/image&image=' + encodeURIComponent(imageName) + image_size,
     dataType: 'text',
     success: function(text) {
        thumb.attr('src', text);
        field.val(imageName);
     }
   });
};

function el_upload() {
   $('#dialog').remove();  
   $('#content').prepend('<div id="dialog" style="padding: 3px 0px 0px 0px;"><iframe src="index.php?route=common/elmanager" style="padding:0; margin: 0; display: block; width: 100%; height: 100%;" frameborder="no" scrolling="auto"></iframe></div>');
   
   $('#dialog').dialog({
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