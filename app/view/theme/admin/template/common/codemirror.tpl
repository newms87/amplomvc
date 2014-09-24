<div class="ac-codemirror-ui">
	<div class="menu-buttons">
		<div class="cm-btn fullscreen" data-action="fullscreen">
			<img class="maximize" src="<?= theme_url('image/codemirror/maximize.png'); ?>" title="Use F11 for true full screen" />
			<img class="minimize" src="<?= theme_url('image/codemirror/minimize.png'); ?>"/>
		</div>
	</div>
</div>

<div id="wysihtml5-editor-toolbar" class="hide">
	<header>
		<ul class="commands">
			<li data-wysihtml5-command="bold" title="Make text bold (CTRL + B)" class="command" href="javascript:;" unselectable="on"></li>
			<li data-wysihtml5-command="italic" title="Make text italic (CTRL + I)" class="command" href="javascript:;" unselectable="on"></li>
			<li data-wysihtml5-command="insertUnorderedList" title="Insert an unordered list" class="command wysihtml5-command-active" href="javascript:;" unselectable="on"></li>
			<li data-wysihtml5-command="insertOrderedList" title="Insert an ordered list" class="command" href="javascript:;" unselectable="on"></li>
			<li data-wysihtml5-command="createLink" title="Insert a link" class="command" href="javascript:;" unselectable="on"></li>
			<li data-wysihtml5-command="insertImage" title="Insert an image" class="command" href="javascript:;" unselectable="on"></li>
			<li data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h1" title="Insert headline 1" class="command" href="javascript:;" unselectable="on"></li>
			<li data-wysihtml5-command="formatBlock" data-wysihtml5-command-value="h2" title="Insert headline 2" class="command" href="javascript:;" unselectable="on"></li>
			<li data-wysihtml5-command-group="foreColor" class="fore-color" title="Color the selected text">
				<ul>
					<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="silver" href="javascript:;" unselectable="on"></li>
					<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="gray" href="javascript:;" unselectable="on"></li>
					<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="maroon" href="javascript:;" unselectable="on"></li>
					<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="red" href="javascript:;" unselectable="on"></li>
					<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="purple" href="javascript:;" unselectable="on" class=""></li>
					<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="green" href="javascript:;" unselectable="on"></li>
					<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="olive" href="javascript:;" unselectable="on" class=""></li>
					<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="navy" href="javascript:;" unselectable="on" class=""></li>
					<li data-wysihtml5-command="foreColor" data-wysihtml5-command-value="blue" href="javascript:;" unselectable="on"></li>
				</ul>
			</li>
			<li data-wysihtml5-command="insertSpeech" title="Insert speech" class="command" href="javascript:;" unselectable="on" style="position: relative;"><div style="left: 0px; margin: 0px; opacity: 0; overflow: hidden; padding: 0px; position: absolute; top: 0px; z-index: 1; width: 70px; height: 40px;"><input x-webkit-speech="" speech="" style="cursor: inherit; font-size: 50px; height: 50px; margin-top: -25px; outline: 0px; padding: 0px; position: absolute; right: -4px; top: 50%;"></div></li>
			<li data-wysihtml5-action="change_view" title="Show HTML" class="action" href="javascript:;" unselectable="on"></li>
		</ul>
	</header>
	<div data-wysihtml5-dialog="createLink" style="display: none;">
		<label>
			Link:
			<input data-wysihtml5-dialog-field="href" value="http://">
		</label>
		<a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>
	</div>

	<div data-wysihtml5-dialog="insertImage" style="display: none;">
		<label>
			Image:
			<input data-wysihtml5-dialog-field="src" value="http://">
		</label>
		<a data-wysihtml5-dialog-action="save">OK</a>&nbsp;<a data-wysihtml5-dialog-action="cancel">Cancel</a>
	</div>
</div>

<script type="text/javascript" src="<?= URL_RESOURCES . 'js/codemirror/codemirror.js'; ?>"></script>

<!-- wysihtml5 parser rules -->
<!--<script src="<?= URL_RESOURCES . "js/wysihtml5/parser_rules/advanced.js"; ?>"></script>
<script src="<?= URL_RESOURCES . "js/wysihtml5/dist/wysihtml5-0.3.0.min.js"; ?>"></script> -->

<script type="text/javascript">
//	var editor = new wysihtml5.Editor("wysihtml5-editor", {
//		parserRules:  wysihtml5ParserRules,
//		toolbar: 'wysihtml5-editor-toolbar',
//		stylesheets: ["http://yui.yahooapis.com/2.9.0/build/reset/reset-min.css", $ac.site_url + "system/resources/js/wysihtml5/css/style.css"]
//	});

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

	$.fn.init_codemirror = function (params) {
		var params = $.extend({}, {
			tabSize: 3,
			indentWithTabs: true,
			lineNumbers: false,
			indentUnit: 3,
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
</script>
