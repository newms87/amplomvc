<?= '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en" xml:lang="en">
<head>
	<title><?= $head_title; ?></title>
	<base href="<?= $base; ?>"/>
	<script type="text/javascript" src="<?= HTTP_JS . "jquery/jquery.js"; ?>"></script>
	<script type="text/javascript" src="<?= HTTP_JS . "jquery/ui/jquery-ui.js"; ?>"></script>
	<link rel="stylesheet" type="text/css"
		href="<?= HTTP_JS . "jquery/ui/themes/ui-lightness/jquery-ui.custom.css"; ?>"/>
	<script type="text/javascript" src="<?= HTTP_JS . "jquery/ajaxupload.js"; ?>"></script>
	<script type="text/javascript" src="<?= HTTP_JS . "common.js"; ?>"></script>
	<script type="text/javascript" src="<?= URL_THEME_JS . "common.js"; ?>"></script>
	<style type="text/css">
		body {
			padding: 0;
			margin: 0;
			background: #F7F7F7;
			font-family: Verdana, Arial, Helvetica, sans-serif;
			font-size: 11px;
		}

		img {
			border: 0;
		}

		#container {
			padding: 0px 10px 7px 10px;
			height: 340px;
		}

		#menu {
			clear: both;
			height: 29px;
			margin-bottom: 3px;
		}

		#column-left {
			background: #FFF;
			border: 1px solid #CCC;
			float: left;
			width: 20%;
			height: 320px;
			overflow: auto;
		}

		#column-right {
			background: #FFF;
			border: 1px solid #CCC;
			float: right;
			width: 78%;
			height: 320px;
			overflow: auto;
			text-align: center;
		}

		#column-right div {
			text-align: left;
			padding: 5px;
		}

		#column-right a {
			display: inline-block;
			text-align: center;
			border: 1px solid #EEEEEE;
			cursor: pointer;
			margin: 5px;
			padding: 5px;
		}

		#column-right a.selected {
			border: 1px solid #7DA2CE;
			background: #EBF4FD;
		}

		#column-right input {
			display: none;
		}

		#dialog {
			display: none;
		}

		.button {
			display: block;
			float: left;
			padding: 8px 5px 8px 25px;
			margin-right: 5px;
			background-position: 5px 6px;
			background-repeat: no-repeat;
			cursor: pointer;
		}

		.button:hover {
			background-color: #EEEEEE;
		}

		.thumb {
			padding: 5px;
			width: 105px;
			height: 105px;
			background: #F7F7F7;
			border: 1px solid #CCCCCC;
			cursor: pointer;
			cursor: move;
			position: relative;
		}
	</style>
</head>
<body>
<div id="container">
	<div id="menu">
		<a id="create" class="button"
			style="background-image: url('<?= URL_THEME_IMAGE . "filemanager/folder.png"; ?>');"><?= _l("Folder"); ?></a>
		<a id="delete" class="button"
			style="background-image: url('<?= URL_THEME_IMAGE . "filemanager/edit-delete.png"; ?>');"><?= _l("Delete"); ?></a>
		<a id="move" class="button"
			style="background-image: url('<?= URL_THEME_IMAGE . "filemanager/edit-cut.png"; ?>');"><?= _l("Move"); ?></a>
		<a id="copy" class="button"
			style="background-image: url('<?= URL_THEME_IMAGE . "filemanager/edit-copy.png"; ?>');"><?= _l("Copy"); ?></a>
		<a id="rename" class="button"
			style="background-image: url('<?= URL_THEME_IMAGE . "filemanager/edit-rename.png"; ?>');"><?= _l("Rename"); ?></a>
		<a id="upload" class="button"
			style="background-image: url('<?= URL_THEME_IMAGE . "filemanager/upload.png"; ?>');"><?= _l("Upload"); ?></a>
		<a id="refresh" class="button"
			style="background-image: url('<?= URL_THEME_IMAGE . "filemanager/refresh.png"; ?>');"><?= _l("Refresh"); ?></a>
	</div>
	<div id="column-left"></div>
	<div id="column-right"></div>
</div>
<script type="text/javascript">
$(document).ready(function () {
	alert("This has not been fully implemented! You will not be able to upload picture to the CKEDITOR this way for now...");
	$('#column-left').tree({
		data: {
			type: 'json',
			async: true,
			opts: {
				method: 'post',
				url: "<?= URL_AJAX . "common/filemanager/directory"; ?>"
			}
		},
		selected: 'top',
		ui: {
			theme_name: 'classic',
			animation: 100
		},
		types: {
			'default': {
				clickable: true,
				creatable: false,
				renameable: false,
				deletable: false,
				draggable: false,
				max_children: -1,
				max_depth: -1,
				valid_children: 'all'
			}
		},
		callback: {
			beforedata: function (NODE, TREE_OBJ) {
				if (NODE == false) {
					TREE_OBJ.settings.data.opts.static = [
						{
							data: 'image',
							attributes: {
								'id': 'top',
								'directory': ''
							},
							state: 'closed'
						}
					];

					return { 'directory': '' }
				} else {
					TREE_OBJ.settings.data.opts.static = false;

					return { 'directory': $(NODE).attr('directory') }
				}
			},
			onselect: function (NODE, TREE_OBJ) {
				$.ajax({
					url: "<?= URL_AJAX . "common/filemanager/files"; ?>",
					type: 'post',
					data: 'directory=" + encodeURIComponent($(NODE).attr("directory')
				),
				dataType: 'json',
					success
				:
				function (json) {
					html = '<div>';

					if (json) {
						for (i = 0; i < json.length; i++) {
							name = '';

							filename = json[i]['filename'];

							for (j = 0; j < filename.length; j = j + 15) {
								name += filename.substr(j, 15) + '<br />';
							}

							name += json[i]['size'];

							html += '<a>' + name + '<input type="hidden" name="image" value="' + json[i]['file'] + '" /></a>';
						}
					}

					html += '</div>';

					$('#column-right').html(html);

					$('#column-right a').each(function (index, element) {
						$.ajax({
							url: "<?= URL_AJAX . "common/filemanager/image"; ?>" + '?image=" + encodeURIComponent("data/' + $(element).find('input[name=\'image\']').attr('value')),
							dataType
						:
						'html',
							success
						:
						function (html) {
							$(element).prepend('<img src="' + html + '" title="" style="display: none;" /><br />');

							$(element).find('img').fadeIn();
						}
					});
				}

				)
				;
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
}
}
})
;

$('#column-right a').live('click', function () {
	if ($(this).attr('class') == 'selected') {
		$(this).removeAttr('class');
	} else {
		$('#column-right a').removeAttr('class');

		$(this).attr('class', 'selected');
	}
});

$('#column-right a').live('dblclick', function () {
	<? if ($CKEditorFuncNum) { ?>
	window.opener.CKEDITOR.tools.callFunction(<?= $CKEditorFuncNum; ?>, '<?= $directory; ?>' + $(this).find('input[name=\'image\']').attr('value'));

	self.close();
	<? } else { ?>
	parent.$('#<?= $field; ?>').attr('value', 'data/' + $(this).find('input[name=\'image\']').attr('value'));
	parent.$('#dialog').dialog('close');

	parent.$('#dialog').remove();
	<? } ?>
});

$('#create').bind('click', function () {
	var tree = $.tree.focused();

	if (tree.selected) {
		$('#dialog').remove();

		html = '<div id="dialog">';
		html += '<?= _l("Folder"); ?> <input type="text" name="name" value="" /> <input type="button" value="<?= _l("Submit"); ?>" />';
		html += '</div>';

		$('#column-right').prepend(html);

		$('#dialog').dialog({
			title: '<?= _l("Folder"); ?>',
			resizable: false
		});

		$('#dialog input[type=\'button\']').bind('click', function () {
			$.ajax({
				url: "<?= URL_AJAX . "common/filemanager/create"; ?>",
				type: 'post',
				data: 'directory=" + encodeURIComponent($(tree.selected).attr("directory')
			)
			+'&name=" + encodeURIComponent($("#dialog input[name=\'name\']'
			).
			val()
			),
			dataType: 'json',
				success
			:
			function (json) {
				if (json.success) {
					$('#dialog').remove();

					tree.refresh(tree.selected);

					alert(json.success);
				} else {
					alert(json.error);
				}
			}

			,
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
	)
	;
}
else
{
	alert('<?= $error_directory; ?>');
}
})
;

$('#delete').bind('click', function () {
	path = $('#column-right a.selected').find('input[name=\'image\']').attr('value');

	if (path) {
		$.ajax({
			url: "<?= URL_AJAX . "common/filemanager/delete"; ?>",
			type: 'post',
			data: 'path=" + encodeURIComponent(path),
			dataType: "json',
			success: function (json) {
				if (json.success) {
					var tree = $.tree.focused();

					tree.select_branch(tree.selected);

					alert(json.success);
				}

				if (json.error) {
					alert(json.error);
				}
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	} else {
		var tree = $.tree.focused();

		if (tree.selected) {
			$.ajax({
				url: "<?= URL_AJAX . "common/filemanager/delete"; ?>",
				type: 'post',
				data: 'path=" + encodeURIComponent($(tree.selected).attr("directory')
		),
			dataType: 'json',
				success
		:
			function (json) {
				if (json.success) {
					tree.select_branch(tree.parent(tree.selected));

					tree.refresh(tree.selected);

					alert(json.success);
				}

				if (json.error) {
					alert(json.error);
				}
			}

		,
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		}
	)
		;
	}
	else
	{
		alert('<?= $error_select; ?>');
	}
}
})
;

$('#move').bind('click', function () {
	$('#dialog').remove();

	html = '<div id="dialog">';
	html += '<?= _l("Move"); ?> <select name="to"></select> <input type="button" value="<?= _l("Submit"); ?>" />';
	html += '</div>';

	$('#column-right').prepend(html);

	$('#dialog').dialog({
		title: '<?= _l("Move"); ?>',
		resizable: false
	});

	$('#dialog select[name=\'to\']').load("<?= URL_AJAX . "common/filemanager/folders"; ?>");

	$('#dialog input[type=\'button\']').bind('click', function () {
		path = $('#column-right a.selected').find('input[name=\'image\']').attr('value');

		if (path) {
			$.ajax({
				url: "<?= URL_AJAX . "common/filemanager/move"; ?>",
				type: 'post',
				data: 'from=" + encodeURIComponent(path) + "&to=" + encodeURIComponent($("#dialog select[name=\'to\']').val()
		),
			dataType: 'json',
				success
		:
			function (json) {
				if (json.success) {
					$('#dialog').remove();

					var tree = $.tree.focused();

					tree.select_branch(tree.selected);

					alert(json.success);
				}

				if (json.error) {
					alert(json.error);
				}
			}

		,
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		}
		)
		;
	}
	else
	{
		var tree = $.tree.focused();

		$.ajax({
			url: "<?= URL_AJAX . "common/filemanager/move"; ?>",
			type: 'post',
			data: 'from=" + encodeURIComponent($(tree.selected).attr("directory')
	)
		+'&to=" + encodeURIComponent($("#dialog select[name=\'to\']'
	).
		val()
	),
		dataType: 'json',
			success
	:
		function (json) {
			if (json.success) {
				$('#dialog').remove();

				tree.select_branch('#top');

				tree.refresh(tree.selected);

				alert(json.success);
			}

			if (json.error) {
				alert(json.error);
			}
		}

	,
		error: function (xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	}
	)
	;
}
})
;
})
;

$('#copy').bind('click', function () {
	$('#dialog').remove();

	html = '<div id="dialog">';
	html += '<?= _l("Copy"); ?> <input type="text" name="name" value="" /> <input type="button" value="<?= _l("Submit"); ?>" />';
	html += '</div>';

	$('#column-right').prepend(html);

	$('#dialog').dialog({
		title: '<?= _l("Copy"); ?>',
		resizable: false
	});

	$('#dialog select[name=\'to\']').load("<?= URL_AJAX . "common/filemanager/folders"; ?>");

	$('#dialog input[type=\'button\']').bind('click', function () {
		path = $('#column-right a.selected').find('input[name=\'image\']').attr('value');

		if (path) {
			$.ajax({
				url: "<?= URL_AJAX . "common/filemanager/copy"; ?>",
				type: 'post',
				data: 'path=" + encodeURIComponent(path) + "&name=" + encodeURIComponent($("#dialog input[name=\'name\']').val()
		),
			dataType: 'json',
				success
		:
			function (json) {
				if (json.success) {
					$('#dialog').remove();

					var tree = $.tree.focused();

					tree.select_branch(tree.selected);

					alert(json.success);
				}

				if (json.error) {
					alert(json.error);
				}
			}

		,
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		}
		)
		;
	}
	else
	{
		var tree = $.tree.focused();

		$.ajax({
			url: "<?= URL_AJAX . "common/filemanager/copy"; ?>",
			type: 'post',
			data: 'path=" + encodeURIComponent($(tree.selected).attr("directory')
	)
		+'&name=" + encodeURIComponent($("#dialog input[name=\'name\']'
	).
		val()
	),
		dataType: 'json',
			success
	:
		function (json) {
			if (json.success) {
				$('#dialog').remove();

				tree.select_branch(tree.parent(tree.selected));

				tree.refresh(tree.selected);

				alert(json.success);
			}

			if (json.error) {
				alert(json.error);
			}
		}

	,
		error: function (xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	}
	)
	;
}
})
;
})
;

$('#rename').bind('click', function () {
	$('#dialog').remove();

	html = '<div id="dialog">';
	html += '<?= _l("Rename"); ?> <input type="text" name="name" value="" /> <input type="button" value="<?= _l("Submit"); ?>" />';
	html += '</div>';

	$('#column-right').prepend(html);

	$('#dialog').dialog({
		title: '<?= _l("Rename"); ?>',
		resizable: false
	});

	$('#dialog input[type=\'button\']').bind('click', function () {
		path = $('#column-right a.selected').find('input[name=\'image\']').attr('value');

		if (path) {
			$.ajax({
				url: "<?= URL_AJAX . "common/filemanager/rename"; ?>",
				type: 'post',
				data: 'path=" + encodeURIComponent(path) + "&name=" + encodeURIComponent($("#dialog input[name=\'name\']').val()
		),
			dataType: 'json',
				success
		:
			function (json) {
				if (json.success) {
					$('#dialog').remove();

					var tree = $.tree.focused();

					tree.select_branch(tree.selected);

					alert(json.success);
				}

				if (json.error) {
					alert(json.error);
				}
			}

		,
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		}
		)
		;
	}
	else
	{
		var tree = $.tree.focused();

		$.ajax({
			url: "<?= URL_AJAX . "common/filemanager/rename"; ?>",
			type: 'post',
			data: 'path=" + encodeURIComponent($(tree.selected).attr("directory')
	)
		+'&name=" + encodeURIComponent($("#dialog input[name=\'name\']'
	).
		val()
	),
		dataType: 'json',
			success
	:
		function (json) {
			if (json.success) {
				$('#dialog').remove();

				tree.select_branch(tree.parent(tree.selected));

				tree.refresh(tree.selected);

				alert(json.success);
			}

			if (json.error) {
				alert(json.error);
			}
		}

	,
		error: function (xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	}
	)
	;
}
})
;
})
;

new AjaxUpload('#upload', {
	action: "<?= URL_AJAX . "common/filemanager/upload"; ?>",
	name: 'image',
	autoSubmit: false,
	responseType: 'json',
	onChange: function (file, extension) {
		var tree = $.tree.focused();

		if (tree.selected) {
			this.setData({'directory': $(tree.selected).attr('directory')});
		} else {
			this.setData({'directory': ''});
		}

		this.submit();
	},
	onSubmit: function (file, extension) {
		$('#upload').append('<img src="<?= theme_url('image/loading.gif'); ?>" class="loading" style="padding-left: 5px;" />');
	},
	onComplete: function (file, json) {
		if (json.success) {
			var tree = $.tree.focused();

			tree.select_branch(tree.selected);

			alert(json.success);
		}

		if (json.error) {
			alert(json.error);
		}

		$('.loading').remove();
	}
});

$('#refresh').bind('click', function () {
	var tree = $.tree.focused();

	tree.refresh(tree.selected);
});
})
;
</script>
</body>
</html>
