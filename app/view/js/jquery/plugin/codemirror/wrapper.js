var $ac_cm_template = $('.ac-codemirror-ui').remove();

$ac_cm_template.keyup(function (event) {
	var $this = $(this);
	var p = false;

	switch (event.keyCode) {
		case 122:
			cm_action($this, 'fullscreen');
			break;
	}

	if (p) {
		event.preventDefault();
		return false;
	}
});

$ac_cm_template.find('.cm-btn').click(function () {
	cm_action($(this).closest('.ac-codemirror-ui'), $(this).attr('data-action'));
});

function cm_action(editor, action) {
	switch (action) {
		case 'fullscreen':
			editor.toggleClass('fullscreen');
			break;
	}
}

$.fn.codemirror = function (params) {
	var params = $.extend({}, {
		tabSize:           3,
		indentWithTabs:    true,
		lineNumbers:       false,
		indentUnit:        3,
		autoCloseBrackets: "()[]{}"
	}, params);

	params.matchBrackets = true;

	switch (params.mode) {
		case 'html':
		case 'htmlmixed':
		case 'php':
			params.mode = 'php';
			break;

		case 'javascript':
		case 'js':
			params.mode = 'javascript';
			break;
	}

	return this.each(function (i, e) {
		e.cm_editor = CodeMirror.fromTextArea(e, params);
		var $clone = $ac_cm_template.clone(true);
		$(e).after($clone.append($(e).siblings('.CodeMirror')));

		if (typeof params.update == 'function') {
			e.cm_editor.on('keyup', params.update);
		}
	});
}
