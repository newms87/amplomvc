//ampFileManager jQuery Plugin
$.ampExtend($.ampFileManager = function () {
}, {
	init: function (o) {
		return this.each(function () {
			var $afm = $(this).addClass('amp-file-manager is-empty');
			var $input = $afm.find('input.amp-fm-input');

			o = $.extend({
				change:         $.ampFileManager.upload,
				progress:       $.ampFileManager.progress,
				success:        $.ampFileManager.success,
				onDone:         $.ampFileManager.onDone,
				onFail:         $.ampFileManager.onFail,
				onSelect:       null,
				onRefresh:      null,
				xhr:            $.ampFileManager.xhr,
				path:           null,
				thumb_width:    $ac.fm_thumb_width || 130,
				thumb_height:   $ac.fm_thumb_height || 100,
				category:       '',
				accept:         $input.attr('accept') || [],
				selectable:     true,
				selectMultiple: true,
				isDroppable:    true,
				dropOn:         null,
				helpText:       null,
				url:            $ac.site_url + 'file/upload',
				listingUrl:     $ac.site_url + 'file/listing',
				listing:        {
					sort:    {'name': 'ASC'},
					filter:  {},
					options: {
						limit: 10
					}
				}
			}, o);

			if (o.category) {
				o.listing.filter.category = o.category;
			}

			$afm.setOptions(o);

			if ($input.length) {
				$afm.ampFileManager('initTemplate');
			} else {
				$.get($ac.site_url + 'file', null, function (response) {
					$afm.append(response).ampFileManager('initTemplate');
				})
			}


		});
	},

	get: function (listing) {
		var $afm = this;
		var o = $afm.getOptions();

		$.post(o.listingUrl, $.extend(true, o.listing, listing), function (response) {
			if (response.files) {
				for (var f in response.files) {
					$afm.ampFileManager('newFile', response.files[f])
				}
			}
		})

		return this;
	},

	select: function ($file) {
		var $afm = this;
		var o = $afm.getOptions();

		if (o.selectable) {
			var selected = !$file.hasClass('selected');

			if (!o.selectMultiple) {
				$afm.find('.amp-fm-file.selected').removeClass('selected');
				$file.addClass('selected');
			} else {
				$file.toggleClass('selected');
			}

			if (selected && o.onSelect) {
				o.onSelect.call($afm, $file, $file.data('file'));
			}

			o.selectedFile = $file.attr('data-file-id');
		}

		return this;
	},

	newFile: function (file) {
		var $afm = this;
		var o = $afm.getOptions();

		var $file = o.fileList.ac_template('afm-file', 'add');

		$file.find('.thumbnail').css({
			width:  o.thumb_width,
			height: o.thumb_height
		});

		if (file) {
			$afm.ampFileManager('updateFile', $file, file);
		}

		$afm.ampFileManager('refresh');

		return $file;
	},

	updateFile: function ($file, file) {
		var $afm = this;
		var o = $afm.getOptions();

		if (file.file_id) {
			$file.attr('data-file-id', file.file_id);
			$file.find('.remove-file').attr('href', $ac.site_url + 'file/remove?file_id=' + file.file_id);

			if (o.selectedFile == file.file_id) {
				$afm.ampFileManager('select', $file);
			}
		}

		if (file.url) {
			$file.find('.thumbnail .thumb-img').html($('<img />').attr('src', file.url))
		}

		if (file.name) {
			$file.find('.name', file.name);
			$file.attr('data-file-name', file.name);
		}

		$file.data('file', file);

		return this;
	},

	removeFile: function ($file) {
		var $afm = this;

		$.ampConfirm({
			text:      "Are you sure you want to remove this file?",
			onConfirm: function () {
				$file.addClass('is-removing');

				$.get($ac.site_url + 'file/remove', {file_id: $file.attr('data-file-id')}, function (response) {
					if (response.success) {
						$file.remove();
						$afm.ampFileManager('refresh');
					} else {
						$file.removeClass('is-removing')
					}

					$afm.show_msg(response);
				})
			}
		})

		return false;
	},

	refresh: function () {
		var $afm = this;
		var o = $afm.getOptions();

		if (o.fileList.children().length) {
			$afm.removeClass('is-empty').addClass('is-filled');
		} else {
			$afm.removeClass('is-filled').addClass('is-empty');
		}

		if (o.onRefresh) {
			o.onRefresh.call($afm);
		}
	},

	upload: function (files) {
		var $afm = this;

		if (!files.length) {
			return $.ampAlert('No Files to upload.');
		}

		for (var i = 0; i < files.length; i++) {
			$afm.ampFileManager('ajaxUpload', files[i]);
		}

		return this;
	},

	ajaxUpload: function (file, overwrite) {
		var $afm = this;
		var o = $afm.getOptions();

		var $existing = o.fileList.find('[data-file-name="' + file.name + '"]');

		if (!overwrite && $existing.length) {
			$.ampConfirm({
				text:      "A file with the name " + file.name + " already exists. Would you like to overwrite this file?",
				onConfirm: function () {
					$existing.remove();
					$afm.ampFileManager('ajaxUpload', file);
				}
			});

			return this;
		}

		var fd = new FormData();

		fd.append('file', file);

		if (typeof o.path === 'string') {
			fd.append('path', o.path);
		}

		if (o.accept) {
			fd.append('accept', o.accept);
		}

		if (o.category) {
			fd.append('category', o.category);
		}

		var $file = $afm.ampFileManager('newFile', file);
		$file.addClass('is-uploading');

		$.ajax({
			url:         o.url,
			data:        fd,
			processData: false,
			contentType: false,
			type:        'POST',
			xhr:         function (e) {
				return $afm.ampFileManager('xhr', $file, e);
			},
			success:     function (response, status, xhr) {
				$file.removeClass('is-uploading');
				return $afm.ampFileManager('success', $file, response, status, xhr);
			}
		});

		return this;
	},

	xhr: function ($file, e) {
		var $afm = this;
		var myXhr = $.ajaxSettings.xhr();

		if (myXhr.upload) {
			myXhr.upload.addEventListener('progress', function (e) {
				return $afm.getOptions().progress.call($afm, $file, e);
			}, false);
		}

		return myXhr;
	},

	onDone: function ($file, files) {
		for (var f in files) {
			this.ampFileManager('updateFile', $file, files[f]);
		}

		return this;
	},

	onFail: function ($file, error) {
		$.show_msg('error', error);
		$file.remove();

		return this;
	},

	success: function ($file, response, status, xhr) {
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

	progress: function ($file, e) {
		var total = typeof e === 'object' ? (e.loaded / e.total) * 100 : e;

		total = total.toFixed(2) + '%';
		$file.find('.progress-msg').html(total);
		$file.find('.progress').css({width: total})
		return this;
	},


	initTemplate: function () {
		var $afm = this;
		var o = $afm.getOptions();

		o.input = $afm.find('.amp-fm-input');
		o.fileList = $afm.find('.amp-fm-file-list');
		o.folderView = $afm.find('.amp-fm-folder-view');

		o.dropOn = $(o.dropOn);

		if (o.isDroppable) {
			o.dropOn = o.dropOn.add($afm.find('.amp-fm-drop'))
		}

		if (o.accept) {
			if (typeof o.accept === 'string') {
				o.accept = o.accept.split(',');
			}

			o.input.attr('accept', o.accept.join(','));
		}

		o.fileList.click(function (e) {
			e.stopPropagation();
			return false;
		})

		o.input.change(function () {
			var $afm = $(this).closest('.amp-file-manager');

			if (this.files.length) {
				$afm.ampFileManager('upload', this.files);
			}
		})

		o.dropOn
			.click(function () {
				o.input.click()
			})
			.on('drop', function (e) {
				var $afm = $(this).closest('.amp-file-manager');

				files = e.originalEvent.dataTransfer.files;

				if (!files) {
					$.ampAlert('Unable to upload files because your browser does not support HTML 5.');
					return;
				}

				$afm.ampFileManager('upload', files);
			})
			.on('dragenter dragover', function (e) {
				$(this).addClass('is-dropping');
				e.preventDefault();
				e.stopPropagation();
			})
			.on('drop dragend dragleave', function (e) {
				$(this).removeClass('is-dropping');
				e.preventDefault();
				return false;
			});

		if (o.helpText) {
			$afm.find('.amp-fm-help').html(o.helpText);
		}

		var $file = $afm.find('.amp-fm-file[data-row=__ac_template__]');

		$file.click(function () {
			$(this).closest('.amp-file-manager').ampFileManager('select', $(this));
		});

		$file.find('.remove-file').click(function () {
			$(this).closest('.amp-file-manager').ampFileManager('removeFile', $(this).closest('.amp-fm-file'));

			return false;
		})

		$file.ac_template('afm-file');

		$afm.ampFileManager('get');

		return this;
	}
});
