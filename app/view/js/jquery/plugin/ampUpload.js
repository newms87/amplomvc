$.ampUpload = $.fn.ampUpload = function (o) {
	return $.amp.call(this, $.ampUpload, arguments);
}

$.extend($.ampUpload, {
	init: function (o) {
		return this.each(function (i, e) {
			o = $.extend({
				change:         $.ampUpload.upload,
				progress:       $.ampUpload.progress,
				success:        $.ampUpload.success,
				url:            $ac.site_url + 'common/file-upload',
				xhr:            $.ampUpload.xhr,
				path:           '',
				preview:        null,
				content:        null,
				msg:            'Click to upload file',
				showInput:      false,
				class:          '',
				progressBar:    true,
				progressBarMsg: false
			}, o);

			var $input = $(e), $upload = $('<div/>').addClass('file-upload-box row ' + o.class);

			$input.after($upload).appendTo($upload).toggle(o.showInput);

			e.o = o;
			e.content = $('<div/>').addClass('content');
			e.msg = $('<div/>').addClass('msg');
			e.bar = $('<div/>').addClass('progress-bar');
			e.bar_msg = $('<div/>').addClass('bar-msg');
			e.progress = $('<div/>').addClass('progress');

			e.bar.append(e.progress);

			if (o.progressBarMsg) {
				e.bar.append(e.bar_msg);
			}

			e.save = $('<input/>').attr('type', 'hidden').attr('name', $input.attr('name')).val($input.val() || e.defaultValue).appendTo($upload);
			e.preview = $($input.attr('data-preview'));

			if (o.content) {
				e.content.html(o.content).appendTo($upload)
					.on('drop', function (event) {
						e.files = event.originalEvent.dataTransfer.files;

						if (!e.files) {
							alert('Your browser does not support HTML 5');
							return;
						}

						o.change.call(e);
					})
					.on('dragenter dragover', function (e) {
						$(this).addClass('hover');
						e.preventDefault();
						e.stopPropagation();
					})
					.on('drop dragend dragleave', function (e) {
						$(this).removeClass('hover');
						e.preventDefault();
						return false;
					});
			}

			if (o.msg) {
				e.msg.html(o.msg).appendTo($upload);
			}

			if (o.progressBar) {
				e.bar.appendTo($upload);
			}

			//Hide Input field
			$input.css({left: -99999});
			$input.click(function (e) {
				e.stopPropagation();
			});

			$upload.click(function () {
				$input.click();
			});

			$input.removeAttr('name');

			if (typeof o.change === 'function') {
				$input.change(o.change);
			}
		});
	},

	upload: function () {
		var $this = this;

		if (!$this.files) {
			return alert('No Files to upload');
		}

		for (var i = 0; i < $this.files.length; i++) {
			var file = $this.files[i];
			var fd = new FormData();

			fd.append('file', file);
			fd.append('path', $this.o.path);

			$.ajax({
				url:         $this.o.url,
				data:        fd,
				processData: false,
				contentType: false,
				type:        'POST',
				xhr:         function (e) {
					this.context = $this;
					return $this.o.xhr.call(this, e);
				},
				success:     function (response, status, xhr) {
					this.context = $this;
					return $this.o.success.call(this, response, status, xhr);
				}
			});
		}
	},

	xhr: function () {
		var $this = this;
		var myXhr = $.ajaxSettings.xhr();

		if (myXhr.upload) {
			myXhr.upload.addEventListener('progress', function (e) {
				this.context = $this.context;
				return $this.context.o.progress.call(this, e);
			}, false);
		}

		return myXhr;
	},

	success: function (response, status, xhr) {
		var o = this.context.o;

		if (response.data) {
			for (var f in response.data) {
				var url = response.data[f];
				this.context.save.val(url);
				this.context.msg.html(url);

				var preview = o.preview ? $(o.preview) : this.context.preview;
				if (preview.length) {
					preview.attr('src', url);
				}

				break;
			}
		}

		o.progress.call(this, 100);
	},

	progress: function (e) {
		//Multiply by 75 to account for the delay of server response
		var total = typeof e === 'object' ? (e.loaded / e.total) * 75 : e;
		total = total.toFixed(1);
		this.context.progress.css({width: total + '%'});
		this.context.msg.html(total + '%');
		this.context.bar_msg.html(total + '%');

		if (total < 100) {
			this.context.bar.addClass('in-progress');
		} else {
			this.context.bar.removeClass('in-progress').addClass('done');
		}
	}
});
