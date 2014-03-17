<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

		<script src="<?= URL_RESOURCES . 'js/'; ?>jquery/jquery.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_RESOURCES . 'js/'; ?>jquery/ui/jquery-ui.js" type="text/javascript" charset="utf-8"></script>
		<script type="text/javascript" src="<?= URL_RESOURCES . 'js/' . "common.js"; ?>"></script>

		<link rel="stylesheet" href="<?= URL_RESOURCES . 'js/'; ?>jquery/ui/themes/ui-lightness/jquery-ui.custom.css" type="text/css"
			media="screen" title="no title" charset="utf-8">

		<link rel="stylesheet" href="<?= URL_ELFINDER; ?>css/common.css" type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?= URL_ELFINDER; ?>css/dialog.css" type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?= URL_ELFINDER; ?>css/toolbar.css" type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?= URL_ELFINDER; ?>css/navbar.css" type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?= URL_ELFINDER; ?>css/statusbar.css" type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?= URL_ELFINDER; ?>css/contextmenu.css" type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?= URL_ELFINDER; ?>css/cwd.css" type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?= URL_ELFINDER; ?>css/quicklook.css" type="text/css" media="screen" charset="utf-8">
		<link rel="stylesheet" href="<?= URL_ELFINDER; ?>css/commands.css" type="text/css" media="screen" charset="utf-8">

		<link rel="stylesheet" href="<?= URL_ELFINDER; ?>css/theme.css" type="text/css" media="screen" charset="utf-8">

		<!-- elfinder core -->
		<script src="<?= URL_ELFINDER; ?>js/elFinder.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/elFinder.version.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/jquery.elfinder.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/elFinder.resources.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/elFinder.options.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/elFinder.history.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/elFinder.command.js" type="text/javascript" charset="utf-8"></script>

		<!-- elfinder ui -->
		<script src="<?= URL_ELFINDER; ?>js/ui/overlay.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/workzone.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/navbar.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/dialog.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/tree.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/cwd.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/toolbar.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/button.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/uploadButton.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/viewbutton.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/searchbutton.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/sortbutton.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/panel.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/contextmenu.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/path.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/stat.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/ui/places.js" type="text/javascript" charset="utf-8"></script>

		<!-- elfinder commands -->
		<script src="<?= URL_ELFINDER; ?>js/commands/back.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/forward.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/reload.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/up.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/home.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/copy.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/cut.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/paste.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/open.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/rm.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/info.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/duplicate.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/rename.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/help.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/getfile.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/mkdir.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/mkfile.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/upload.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/download.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/edit.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/quicklook.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/quicklook.plugins.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/extract.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/archive.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/search.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/view.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/resize.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/sort.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= URL_ELFINDER; ?>js/commands/selectforproduct.js" type="text/javascript" charset="utf-8"></script>

		<!-- elfinder languages -->
		<script src="<?= URL_ELFINDER; ?>js/i18n/elfinder.en.js" type="text/javascript" charset="utf-8"></script>

		<!-- elfinder dialog -->
		<script src="<?= URL_ELFINDER; ?>js/jquery.dialogelfinder.js" type="text/javascript" charset="utf-8"></script>

		<!-- elfinder 1.x connector API support -->
		<script src="<?= URL_ELFINDER; ?>js/proxy/elFinderSupportVer1.js" type="text/javascript" charset="utf-8"></script>

		<style type="text/css">
			body {
				font-family: arial, verdana, sans-serif;
			}

			.button {
				width: 100px;
				position: relative;
				display: -moz-inline-stack;
				display: inline-block;
				vertical-align: top;
				zoom: 1;
				*display: inline;
				margin: 0 3px 3px 0;
				padding: 1px 0;
				text-align: center;
				border: 1px solid #ccc;
				background-color: #eee;
				margin: 1em .5em;
				padding: .3em .7em;
				border-radius: 5px;
				-moz-border-radius: 5px;
				-webkit-border-radius: 5px;
				cursor: pointer;
			}
		</style>

		<script type="text/javascript" charset="utf-8">
			var elfinder_root_dir = '<?= $elfinder_root_dir; ?>';

			$().ready(function () {
				var keyField = "field";
				var valueField = getQueryString(keyField, null);

				if (valueField == "imagemanager") {
					$('#finder').elfinder({
						url: '<?= URL_ELFINDER; ?>php/connector.php?field=imagemanager',
						lang: 'en',
						resizable: 'false',
						commands: [
							'open', 'reload', 'home', 'up', 'back', 'forward', 'getfile', 'quicklook',
							'download', 'rm', 'duplicate', 'rename', 'mkdir', 'mkfile', 'upload', 'copy',
							'cut', 'paste', 'edit', 'extract', 'archive', 'search', 'info', 'view', 'help',
							'resize', 'sort'
						],
						contextmenu: {
							// navbarfolder menu
							navbar: ['open', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', '|', 'info'],

							// current directory menu
							cwd: ['reload', 'back', '|', 'upload', 'mkdir', 'mkfile', 'paste', '|', 'info'],

							// current directory file menu
							files: [
								'getfile', '|', 'open', 'quicklook', '|', 'download', '|', 'copy', 'cut', 'paste', 'duplicate', '|',
								'rm', '|', 'edit', 'rename', 'resize', '|', 'archive', 'extract', '|', 'info'
							]
						},
					});
				} else {
					$('#finder').elfinder({
						url: '<?= URL_ELFINDER; ?>php/connector.php',
						lang: 'en',
						resizable: 'false',
						commands: [
							'open', 'reload', 'home', 'up', 'back', 'forward', 'getfile', 'quicklook',
							'download', 'rm', 'duplicate', 'rename', 'mkdir', 'mkfile', 'upload', 'copy',
							'cut', 'paste', 'edit', 'extract', 'archive', 'search', 'info', 'view', 'help',
							'resize', 'sort', 'selectforproduct'
						],
						contextmenu: {
							// navbarfolder menu
							navbar: ['open', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', '|', 'info'],

							// current directory menu
							cwd: ['reload', 'back', '|', 'upload', 'mkdir', 'mkfile', 'paste', '|', 'info'],

							// current directory file menu
							files: [
								'getfile', '|', 'open', 'quicklook', '|', 'download', '|', 'copy', 'cut', 'paste', 'duplicate', '|',
								'rm', '|', 'edit', 'rename', 'resize', '|', 'archive', 'extract', '|', 'info', '|', 'selectforproduct'
							]
						},
					});
				}
			});
		</script>

	</head>
	<body>
		<div id="finder"></div>
	</body>
</html>
