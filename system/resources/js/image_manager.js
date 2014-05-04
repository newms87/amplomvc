var _acicount = 0;

$.fn.ac_imageinput = function (options) {
	this.each(function (i, e) {
		var $input = $(e);

		if ($input.hasClass("image-field")) {
			return true;
		}

		$input.addClass('image-field');

		options = $.extend({}, {
			class:       'click-image',
			allow_clear: false,
			show_help:   false,
			show_hover:  true,
			noimage:     $.ac_vars.url_site + 'image/no_image.png',
			width:       $input.attr('data-width') || $.ac_vars.image_thumb_width || 140,
			height:      $input.attr('data-height') || $.ac_vars.image_thumb_height || 140
		}, options);

		var $imageinput = $('<div />').addClass('imageinput-box').addClass(options.class);
		$input.before($imageinput);

		var $thumb = $('<img />').addClass('thumb').width(options.width).height(options.height);
		var thumb = $input.attr('data-thumb');

		if (!thumb) {
			thumb = options.noimage;

			if ($input.val()) {
				$.get($.ac_vars.url_site + 'admin/filemanager/filemanager/get_thumb', {image: $input.val()}, function (response) {
					$thumb.attr('src', response);
				});
			}
		}

		$thumb.attr('src', thumb);

		$imageinput.append($thumb);

		if (!$input.attr('id')) {
			$input.attr('id', 'aciimage-' + (_acicount++));
			$input.attr('onchange','$(this).trigger(\'update_image_thumb\')').on('update_image_thumb',function(){
				$thumb.attr('src', $input.attr('data-thumb'));
				$input.val($input.val().replace(/^\/image\//, ''));
			});
		}

		$imageinput.append($input);

		if (options.show_hover) {
			var $hover = $('<img />').attr('src', options.noimage).addClass('hover-change');
			$imageinput.append($hover);
		}

		if (options.allow_clear) {
			$input.clear_image = function () {
				$input.val('');
				$thumb.attr('src', options.noimage);
			}

			var $clear_image = $('<a />').addClass('clear-image').click($input.clear_image);

			if (typeof allow_clear == 'string') {
				$clear_image.append(allow_clear);
			}

			$imageinput.append($clear_image);
		}

		$imageinput.click(function () {
			$.ac_filemanager({field_id: $input.attr('id'), fm_type: 'image'});
		});
	});

	return this;
}

$.ac_filemanager = function (options) {
	var options = $.extend({}, {
		field_id: null,
		fm_type:  'image'
	}, options);

	$('#ac-filemanager').remove();

	var type = 0;

	switch (options.fm_type) {
		case 'video':
			type = 3;
			break;
		case 'image':
			type = 1;
			break;
	}

	var url = $.ac_vars.url_site + 'system/resources/js/responsive-filemanager/filemanager/dialog.php?type=' + type;

	if (options.field_id) {
		url += '&field_id=' + options.field_id;
	}

	var $close = $('<div class="close" />');
	var $acfm = $('<div id="ac-filemanager" />');
	var $iframe = $('<iframe />').attr('src', url);

	$close.click(function () {
		$acfm.remove();
	});

	var pos = $.cookie('ac-filemanager');

	if (pos) {
		try {
			pos = $.parseJSON(pos);
			half_window = $(window).scrollTop() + ($(window).height() / 2);

			pos.top += $(window).scrollTop();

			if (pos.top > half_window) {
				pos.top = half_window;
			}

			$acfm.css(pos);
		} catch(e){}
	}

	$acfm.append($iframe).append($close).draggable({
		drag: function() {
			var newpos = $('#ac-filemanager').offset();

			if (newpos.top > $(window).height()) {
				$acfm.css({top: $(window).scrollTop() + ($(window).height() / 2)});
			}
		},
		stop: function () {
			var newpos = $('#ac-filemanager').offset();

			if (newpos.top > $(window).height()) {
				$acfm.css({top: $(window).scrollTop() + ($(window).height() / 2)});
			}

			newpos.top -= $(window).scrollTop();

			$.cookie('ac-filemanager', JSON.stringify(newpos));
		}
	});

	$('body').append($acfm);
}
