var _acicount = 0;

$.fn.ac_imageinput = function (options) {
	this.use_once('image-field').each(function (i, e) {
		var $input = $(e);

		options = $.extend({}, {
			class:       'click-image',
			allow_clear: false,
			show_help:   false,
			show_hover:  false,
			noimage:     $ac.theme_url + 'image/no_image.png',
			width:       $input.attr('data-width') || $ac.image_thumb_width || 140,
			height:      $input.attr('data-height') || $ac.image_thumb_height || 140
		}, options);

		if (!options.width || options.width == '0') {
			options.width = 'auto';
		}

		if (!options.height || options.height == '0') {
			options.height = 'auto';
		}

		var $imageinput = $('<div />').addClass('imageinput-box').addClass(options.class);
		$input.before($imageinput);

		var $thumb = $('<img />').addClass('thumb').width(options.width).height(options.height);
		var thumb = $input.attr('data-thumb');

		if (!thumb) {
			thumb = options.noimage;

			if ($input.val()) {
				$.get($ac.site_url + 'admin/filemanager/get_thumb', {
					image:  $input.val(),
					width:  options.width,
					height: options.height
				}, function (response) {
					$thumb.attr('src', response);
				});
			}
		}

		$thumb.attr('src', thumb);

		$imageinput.append($thumb);

		if (!$input.attr('id')) {
			$input.attr('id', 'aciimage-' + (_acicount++));
			$input.attr('onchange', '$(this).trigger(\'update_image_thumb\')').on('update_image_thumb', function () {
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
		fm_type:  'image',
		width:    window.innerWidth * .8,
		height:   window.innerHeight * .9
	}, options);

	var type = 0;

	switch (options.fm_type) {
		case 'video':
			type = 3;
			break;
		case 'image':
			type = 1;
			break;
	}

	options.iframe = true;

	if (!options.href) {
		var url = $ac.site_url + 'admin/filemanager' + '?type=' + type;

		if (options.field_id) {
			url += '&field_id=' + options.field_id;
		}

		options.href = url;
	}

	$.colorbox(options);
}
