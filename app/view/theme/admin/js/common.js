$(document).ready(function () {
	//Link List hover menu w/ < IE9 Compatibility
	$('.link-list li').hover(function () {
			$(this).addClass('hover');
		},
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
