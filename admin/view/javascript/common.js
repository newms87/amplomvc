function getQuerystring(key, defaultValue) {
	if(defaultValue == null) defaultValue = "";
	key = key.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
	var regex = new RegExp("[\\?&]" + key + "=([^&#]*)");
	var qs = regex.exec(window.location.href);
	if(qs == null)
		return defaultValue;
	else
		return qs[1];
}

//A jQuery Plugin to update the sort orders columns (or any column needing to be indexed)
$.fn.update_index = function(column) {
	column = column || '.sort_order';
	
	return this.each(function(i,ele){
		count = 0;
		$(ele).find(column).each(function(i,e){
			$(e).val(count++);
		});
	});
}

$.fn.flash_highlight = function() {
	pos = this.offset();
	
	var ele = $('<div />');
	
	ele.css({
			background: 'rgba(255,255,255,0)',
			position: 'absolute',
			top: pos.top,
			left: pos.left,
			opacity: .8,
			'z-index': 10000
		})
		.width($(this).width())
		.height($(this).height());
	
	$('body').css({position: 'relative'});
	$('body').append(ele);
	
	ele.animate({'background-color': 'rgba(255,255,85,1)'}, {duration: 300, always: function(){
		ele.animate({'background-color': 'rgba(255,255,255,0)'}, {duration: 700, always: function(){ele.remove()}});
	}});
	
	return this;
}

String.prototype.str_replace = function(find, replace) {
  var str = this;
  for (var i = 0; i < find.length; i++) {
    str = str.replace(find[i], replace[i]);
  }
  return str;
};

//-----------------------------------------
// Submit form on enter key
// Confirm Actions (delete, uninstall)
// Drop down menu
//-----------------------------------------
$(document).ready(function(){
	//Submit form on enter key
	$('form input').keydown(function(e) {
		if (e.keyCode == 13) {
			$(this).closest('form').submit();
		}
	});
	
    // Confirm Delete
    $('#form').submit(function(){
        if ($(this).attr('action').indexOf('delete',1) != -1) {
            if (!confirm('Deleting this entry will completely remove all data associated from the system. Are you sure?')) {
                return false;
            }
        }
    });
    
    $('.action-delete').click(function(){
		return confirm("Deleting this entry will completely remove all data associated from the system. Are you sure?");
    });
    	
    // Confirm Uninstall
    $('a').click(function(){
        if ($(this).attr('href') != null && $(this).attr('href').indexOf('uninstall', 1) != -1) {
            if (!confirm('Uninstalling will completely remove the data associated from the system. Are you sure?')) {
                return false;
            }
        }
    });
    
    
    //toggle active state for drop down menus
	$('.link_list li').mouseover(function(){
		if(!$(this).hasClass('hover')){
			$(this).closest('.top_menu').find('.hover').removeClass('hover');
			$(this).addClass('hover').parents('.link_list li').addClass('hover');
		}
	});

});

function show_msg(type, html){
	if ($('#content .' + type).length) {
		$('#content .' + type).append('<br />' + html);
	} else {
		$('#content').prepend('<div class="message_box ' + type + '" style="display: none;">' + html + '<span class="close"></span></div>');
		$('.message_box.'+type).fadeIn('slow');
		$('.message_box .close').click(function(){$(this).parent().remove();});
	}
}

function show_msgs(data){
	for (var m in data) {
		if (typeof data[m] == 'object') {
			msg = '';
			
			for (var m2 in data[m]) {
				msg += (msg ? '<br />' : '') + data[m][m2]; 
			}
			
			show_msg(m + ' ' + m2, msg, true);
		}
		else{
			show_msg(m, data[m], true);
		}
	}
}

if(!console){
	console = {};
	console.log = function(msg){};
	console.dir = function(obj){};
}
