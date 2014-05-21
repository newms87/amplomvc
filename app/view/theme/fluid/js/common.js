$.fn._block = function(path, params) {
	return this.load($ac.site_url + 'block/' + path, params);
}

$.add_to_cart = function (params) {
	var data;

	if (params.form && $(params.form).length) {
		data = $(params.form).serialize();
	} else {
		data = {
			product_id: params.product_id || 0,
			quantity: params.quantity || 1
		};

		//Process Options if set
		if (params.options && params.options.length) {
			data['options'] = params.options.serialize();
		}
	}

	params.context.attr('disabled', true);

	if (params.loader) {
		params.context.parent().loading();
	}

	if (typeof params.before === 'function') {
		params.before(params, data);
	}

	$.ajax({
		url: $ac.site_url,
		type: 'post',
		data: data,
		dataType: 'json',
		success: function (json) {
			if (json['error'] || json['success']) {
				$.ac_msg(json);
			}
		},
		complete: function (jqXHR, status) {
			params.context.attr('disabled', false);

			if (params.loader) {
				$.loading('stop');
			}

			if (status === 'parsererror') {
				context.ac_msg('error', jqXHR.responseText);
			}

			if (typeof params.after === 'function') {
				params.after(params, jqXHR, status);
			}
		}
	});
}

$.fn.add_to_cart = function (params) {
	params = $.extend({}, {
		form: null,
		product_id: 0,
		options: null,
		quantity: 1,
		before: null,
		after: null,
		loader: true,
		context: this
	}, params);

	if (!params.form && params.product_id === 0) {
		console.error("$.fn.add_to_cart(): You must provide either the form element or the product_id in the parameters!");
		return false;
	}

	this.click(function () {
		$.add_to_cart(params);
	});

	return this;
}

function scroll_to(dest, duration, context) {
	duration = duration === 0 ? 0 : (duration || 400);
	context = context || $('body');
	if (typeof dest == 'string') dest = $(dest);

	if (!dest.length) return;

	new_top = dest.offset().top;

	max = context.height() - $(window).height();

	if (new_top == context.scrollTop()) return;

	if (new_top > context.scrollTop()) {
		do_scroll = context.scrollTop() < max;
	}
	else {
		do_scroll = context.scrollTop() > 0;
	}

	if (do_scroll) {
		context.animate({scrollTop: new_top}, duration);
	}
}

function submit_block(type, url, form) {
	$.post(url, form.serialize(),
		function (json) {
			if (json['error']) {
				show_msg('warning', json['error']);
				$('body').trigger(type + '_error', json);
			}
			else if (json['success']) {
				show_msg('success', json['success']);
				$('body').trigger(type + '_success', json);
			}
		}
		, 'json');
}

function load_block(context, route, data) {
	data = data || {};

	context.load(route, data, function () {
		context.trigger('loaded')
	});
}

function handle_ajax_error(jqXHR, status) {
	if (jqXHR.responseText.length < 1000) {
		msg = jqXHR.responseText;
	} else {
		msg = '';
	}

	show_msg('warning', 'There was an error with the ajax request. ' + msg);

	console.log('Ajax Error: ' + jqXHR.responseText);
}
