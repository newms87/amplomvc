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

	$('.link-list li').hover(hoverin,
		function () {
			var c = $(this).removeClass('hover');
			if (c.hasClass('has-children') && !c.closest('.top-menu').is(':hover')) {
				c.addClass('inactive');
				setTimeout(function () {
					c.removeClass('inactive')
				}, 500);
			}
		});
});

/* For < IE 9 compatibility */
function hoverin() {
	$(this).addClass('hover');
}

function hoverout() {
	$(this).removeClass('hover');
}