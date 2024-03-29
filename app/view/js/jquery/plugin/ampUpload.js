//ampUpload jQuery Plugin
$.ampExtend($.ampUpload = function() {}, {
	init: function(o) {
		return this.each(function(i, e) {
			var $input = $(e), today = new Date();

			o = $.extend({
				change:         $.ampUpload.upload,
				progress:       $.ampUpload.progress,
				success:        $.ampUpload.success,
				onComplete:     $.ampUpload.onComplete,
				onFail:         $.ampUpload.onFail,
				url:            $ac.site_url + 'common/file-upload',
				xhr:            $.ampUpload.xhr,
				path:           today.getFullYear() + '/' + today.getMonth() + '/' + today.getDate(),
				preview:        null,
				content:        null,
				msg:            'Click to upload file',
				processingMsg:  'Processing... please wait',
				completeMsg:    'Upload Complete. Click to Upload Another File.',
				showInput:      false,
				class:          '',
				progressBar:    true,
				progressBarMsg: false,
				accept:         $input.attr('accept') || []
			}, o);

			var $upload = $('<div/>').addClass('file-upload-box row ' + o.class);

			if (o.accept) {
				if (typeof o.accept === 'string') {
					o.accept = o.accept.split(',');
				}

				$input.attr('accept', o.accept.join(','));
			}

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
					.on('drop', function(event) {
						e.files = event.originalEvent.dataTransfer.files;

						if (!e.files) {
							$.ampAlert('Your browser does not support HTML 5');
							return;
						}

						o.change.call(e);
					})
					.on('dragenter dragover', function(e) {
						$(this).addClass('hover');
						e.preventDefault();
						e.stopPropagation();
					})
					.on('drop dragend dragleave', function(e) {
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
			$input.click(function(e) {
				e.stopPropagation();
			});

			$upload.click(function() {
				$input.click();
			});

			$input.removeAttr('name');

			if (typeof o.change === 'function') {
				$input.change(o.change);
			}
		});
	},

	upload: function() {
		var $this = this;

		if (!$this.files) {
			return $.ampAlert('No Files to upload');
		}

		for (var i = 0; i < $this.files.length; i++) {
			var file = $this.files[i];
			var fd = new FormData();

			fd.append('file', file);
			fd.append('path', $this.o.path);

			if ($this.o.accept) {
				fd.append('accept', $this.o.accept);
			}

			$.ajax({
				url:         $this.o.url,
				data:        fd,
				processData: false,
				contentType: false,
				type:        'POST',
				xhr:         function(e) {
					this.context = $this;
					return $this.o.xhr.call(this, e);
				},
				success:     function(response, status, xhr) {
					this.context = $this;
					return $this.o.success.call(this, response, status, xhr);
				}
			});
		}
	},

	xhr: function() {
		var $this = this;
		var myXhr = $.ajaxSettings.xhr();

		if (myXhr.upload) {
			myXhr.upload.addEventListener('progress', function(e) {
				this.context = $this.context;
				return $this.context.o.progress.call(this, e);
			}, false);
		}

		return myXhr;
	},

	reset: function(o) {
		o.progress.call(this, 0);
		this.msg.html(o.msg);
	},

	onComplete: function(files, o) {
		this.msg.html(o.completeMsg);

		for (var f in files) {
			var url = files[f];
			this.save.val(url);
			if (o.completeMsg === true) {
				this.msg.html(url);
			}

			var preview = o.preview ? $(o.preview) : this.preview;
			if (preview.length) {
				preview.attr('src', url);
			}

			break;
		}
	},

	onFail: function(error, o) {
		$.show_msg('error', error);

		$.ampUpload.reset.call(this, o);
	},

	success: function(response, status, xhr) {
		var o = this.context.o;

		if (response.error) {
			if (typeof o.onFail === 'function') {
				o.onFail.call(this.context, response.error, o);
			}
		} else {
			if (typeof o.onComplete === 'function') {
				o.onComplete.call(this.context, response.data, o);
			}
		}
	},

	progress: function(e) {
		var total = typeof e === 'object' ? (e.loaded / e.total) * 100 : e;
		var ctx = this.context || this;

		if (typeof e === 'string') {
			ctx.bar_msg.html(e);
		} else {
			total = total.toFixed(1);
			ctx.progress.css({width: total + '%'});
			ctx.msg.html(total + '%');
			ctx.bar_msg.html(total + '%');

			if (total < 100) {
				ctx.bar.addClass('in-progress');
			} else {
				ctx.bar.removeClass('in-progress').addClass('done');
				ctx.msg.html(ctx.o.processingMsg || '100%');
			}
		}
	}
});
