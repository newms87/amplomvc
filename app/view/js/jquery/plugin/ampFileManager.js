//ampFileManager jQuery Plugin
$.ampExtend($.ampFileManager = function() {}, {
	init: function(o) {
		return this.each(function() {
			var $afm = $(this).addClass('amp-file-manager'), today = new Date();
			var $input = $afm.find('input.amp-fm-input');

			o = $.extend({
				change:         $.ampFileManager.upload,
				progress:       $.ampFileManager.progress,
				success:        $.ampFileManager.success,
				onDone:         $.ampFileManager.onDone,
				onFail:         $.ampFileManager.onFail,
				url:            $ac.site_url + 'file/upload',
				xhr:            $.ampFileManager.xhr,
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

			$afm.setOptions(o);

			if ($input.length) {
				$afm.ampFileManager('initTemplate');
			} else {
				$afm.load($ac.site_url + 'file/manager', null, function() {
					$afm.ampFileManager('initTemplate');
				})
			}
		});
	},

	initTemplate: function() {
		var $afm = this;
		var o = $afm.getOptions();

		o.input = $afm.find('.amp-fm-input');
		o.drop = $afm.find('.amp-fm-drop');
		o.filelist = $afm.find('.amp-fm-file-list');

		if (o.accept) {
			if (typeof o.accept === 'string') {
				o.accept = o.accept.split(',');
			}

			o.input.attr('accept', o.accept.join(','));
		}

		o.filelist.click(function(e) {
			e.stopPropagation();
			return false;
		})

		o.input.change(function() {
			var $afm = $(this).closest('.amp-file-manager');
			console.log('change', this.files);

			if (this.files.length) {
				$afm.ampFileManager('upload', this.files);
			}
		})

		o.drop
			.click(function() {o.input.click()})
			.on('drop', function(e) {
				var $afm = $(this).closest('.amp-file-manager');

				files = e.originalEvent.dataTransfer.files;

				if (!files) {
					$.ampAlert('Unable to upload files because your browser does not support HTML 5.');
					return;
				}

				$afm.ampFileManager('upload', files);
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

		var $file = $afm.find('.file[data-row=__ac_template__]');

		$file.ac_template('afm-file');

		return this;
	},

	upload: function(files) {
		var $afm = this;
		var o = $afm.getOptions();

		if (!files.length) {
			return $.ampAlert('No Files to upload.');
		}

		for (var i = 0; i < files.length; i++) {
			var $file = o.filelist.ac_template('afm-file', 'add');
			$file.find('.thumbnail').append('hello world');

			var file = files[i];
			var fd = new FormData();

			fd.append('file', file);
			fd.append('path', o.path);

			if (o.accept) {
				fd.append('accept', o.accept);
			}

			$.ajax({
				url:         o.url,
				data:        fd,
				processData: false,
				contentType: false,
				type:        'POST',
				xhr:         function(e) {
					return $afm.ampFileManager('xhr', $file, e);
				},
				success:     function(response, status, xhr) {
					return $afm.ampFileManager('success', $file, response, status, xhr);
				}
			});
		}

		return this;
	},

	xhr: function($file, e) {
		console.log('xhr', $file);
		var $afm = this;
		var myXhr = $.ajaxSettings.xhr();

		if (myXhr.upload) {
			myXhr.upload.addEventListener('progress', function(e) {
				return $afm.getOptions().progress.call($afm, $file, e);
			}, false);
		}

		return myXhr;
	},

	reset: function(o) {
		o.progress.call(this, 0);
		this.msg.html(o.msg);

		return this;
	},

	onDone: function($file, files, o) {
		var $afm = this;
		var o = $afm.getOptions();

		for (var f in files) {
			var url = files[f];

			$file.find('.thumbnail').html($('<img />').attr('src', url))
		}

		return this;
	},

	onFail: function($file, error) {
		var $afm = this;
		$.show_msg('error', error);
		$afm.ampFileManager('reset');
		return this;
	},

	success: function($file, response, status, xhr) {
		var $afm = this;
		var o = $afm.getOptions();

		if (response.error) {
			if (typeof o.onFail === 'function') {
				o.onFail.call($afm, $file, response.error);
			}
		} else {
			if (typeof o.onDone === 'function') {
				o.onDone.call($afm, $file, response.data);
			}
		}

		return this;
	},

	progress: function($file, e) {
		var $afm = this;
		var o = $afm.getOptions();
		var total = typeof e === 'object' ? (e.loaded / e.total) * 100 : e;

		console.log('progress', $file, total, e, o);

		$file.find('.progress-msg').html(total + '%');
		$file.find('.progress').css({width: total + '%'})
		return this;
	}
});
