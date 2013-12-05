//-----------------------------------------
// Submit form on enter key
// Confirm Actions (delete, uninstall)
// Drop down menu
//-----------------------------------------
$(document).ready(function () {
	//Submit form on enter key
	$('form input').keydown(function (e) {
		if (e.keyCode == 13) {
			$(this).closest('form').submit();
		}
	});

	// Confirm Delete
	$('#form').submit(function () {
		if ($(this).attr('action').indexOf('delete', 1) != -1) {
			if (!confirm('Deleting this entry will completely remove all data associated from the system. Are you sure?')) {
				return false;
			}
		}
	});

	$('.action-delete').click(function () {
		return confirm("Deleting this entry will completely remove all data associated from the system. Are you sure?");
	});

	// Confirm Uninstall
	$('a').click(function () {
		if ($(this).attr('href') != null && $(this).attr('href').indexOf('uninstall', 1) != -1) {
			if (!confirm('Uninstalling will completely remove the data associated from the system. Are you sure?')) {
				return false;
			}
		}
	});

	$('.link_list li').hover(hoverin,
		function(){
			var c = $(this).removeClass('hover');
			if (c.hasClass('has_children') && !c.closest('.top_menu').is(':hover')) {
				c.addClass('inactive');
				setTimeout(function(){c.removeClass('inactive')},500);
			}
		});
});

/* For < IE 9 compatibility */
function hoverin(){
	$(this).addClass('hover');
}

function hoverout(){
	$(this).removeClass('hover');
}

function handle_response(response) {
	if (typeof response === 'string') {
		show_msg('error', response);
	} else {
		if (response['redirect']) {
			location = response['redirect'];
			return;
		}

		for (m in response) {
			show_msg(m, response[m], 1000);
		}
	}
}

function show_msg(type, html, showFor) {
	var box = null;

	if ($('.content:first .' + type).length) {
		box = $('.content:first .' + type).append('<br />' + html).show();
	} else {
		$('.content:first').prepend('<div class="message_box ' + type + '" style="display: none;">' + html + '<span class="close"></span></div>');
		box = $('.message_box.' + type).fadeIn('slow');
		$('.message_box .close').click(function () {
			$(this).parent().remove();
		});
	}

	if (showFor) {
		box.delay(showFor).fadeOut(300, function(){$(this).remove()});
	}
}

function show_msgs(data) {
	for (var m in data) {
		if (typeof data[m] == 'object') {
			msg = '';

			for (var m2 in data[m]) {
				msg += (msg ? '<br />' : '') + data[m][m2];
			}

			show_msg(m + ' ' + m2, msg, true);
		}
		else {
			show_msg(m, data[m], true);
		}
	}
}

if (!console) {
	console = {};
	console.log = function (msg) {};
	console.dir = function (obj) {};
}
