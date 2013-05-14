<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<script src="<?= ELFINDER_URL;?>jquery/jquery-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>jquery/jquery-ui-1.8.18.custom.min.js" type="text/javascript" charset="utf-8"></script>
	<link rel="stylesheet" href="<?= ELFINDER_URL;?>jquery/ui-themes/smoothness/jquery-ui-1.8.18.custom.css" type="text/css" media="screen" title="no title" charset="utf-8">

	<link rel="stylesheet" href="<?= ELFINDER_URL;?>css/common.css"      type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="<?= ELFINDER_URL;?>css/dialog.css"      type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="<?= ELFINDER_URL;?>css/toolbar.css"     type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="<?= ELFINDER_URL;?>css/navbar.css"      type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="<?= ELFINDER_URL;?>css/statusbar.css"   type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="<?= ELFINDER_URL;?>css/contextmenu.css" type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="<?= ELFINDER_URL;?>css/cwd.css"         type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="<?= ELFINDER_URL;?>css/quicklook.css"   type="text/css" media="screen" charset="utf-8">
	<link rel="stylesheet" href="<?= ELFINDER_URL;?>css/commands.css"    type="text/css" media="screen" charset="utf-8">
	
	<link rel="stylesheet" href="<?= ELFINDER_URL;?>css/theme.css"       type="text/css" media="screen" charset="utf-8">
	
	<!-- elfinder core -->
	<script src="<?= ELFINDER_URL;?>js/elFinder.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/elFinder.version.js"   type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/jquery.elfinder.js"    type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/elFinder.resources.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/elFinder.options.js"   type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/elFinder.history.js"   type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/elFinder.command.js"   type="text/javascript" charset="utf-8"></script>
	
	<!-- elfinder ui -->
	<script src="<?= ELFINDER_URL;?>js/ui/overlay.js"       type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/workzone.js"      type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/navbar.js"        type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/dialog.js"        type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/tree.js"          type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/cwd.js"           type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/toolbar.js"       type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/button.js"        type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/uploadButton.js"  type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/viewbutton.js"    type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/searchbutton.js"  type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/sortbutton.js"    type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/panel.js"         type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/contextmenu.js"   type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/path.js"          type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/stat.js"          type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/ui/places.js"        type="text/javascript" charset="utf-8"></script>
	
	<!-- elfinder commands -->
	<script src="<?= ELFINDER_URL;?>js/commands/back.js"      type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/forward.js"   type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/reload.js"    type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/up.js"        type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/home.js"      type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/copy.js"      type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/cut.js"       type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/paste.js"     type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/open.js"      type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/rm.js"        type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/info.js"      type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/duplicate.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/rename.js"    type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/help.js"      type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/getfile.js"   type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/mkdir.js"     type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/mkfile.js"    type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/upload.js"    type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/download.js"  type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/edit.js"      type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/quicklook.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/quicklook.plugins.js" type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/extract.js"   type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/archive.js"   type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/search.js"    type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/view.js"      type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/resize.js"    type="text/javascript" charset="utf-8"></script>
	<script src="<?= ELFINDER_URL;?>js/commands/sort.js"      type="text/javascript" charset="utf-8"></script>
    <script src="<?= ELFINDER_URL;?>js/commands/selectforproduct.js"      type="text/javascript" charset="utf-8"></script>		

	<!-- elfinder languages -->
	<script src="<?= ELFINDER_URL;?>js/i18n/elfinder.en.js"    type="text/javascript" charset="utf-8"></script>	

	<!-- elfinder dialog -->
	<script src="<?= ELFINDER_URL;?>js/jquery.dialogelfinder.js"     type="text/javascript" charset="utf-8"></script>

	<!-- elfinder 1.x connector API support -->
	<script src="<?= ELFINDER_URL;?>js/proxy/elFinderSupportVer1.js" type="text/javascript" charset="utf-8"></script>

	<!-- elfinder common javascript -->
	<script type="text/javascript" src="view/javascript/common.js"></script>

	<style type="text/css">
		body { font-family:arial, verdana, sans-serif;}
		.button {
			width: 100px;
			position:relative;
			display: -moz-inline-stack;
			display: inline-block;
			vertical-align: top;
			zoom: 1;
			*display: inline;
			margin:0 3px 3px 0;
			padding:1px 0;
			text-align:center;
			border:1px solid #ccc;
			background-color:#eee;
			margin:1em .5em;
			padding:.3em .7em;
			border-radius:5px; 
			-moz-border-radius:5px; 
			-webkit-border-radius:5px;
			cursor:pointer;
		}
	</style>

<script type="text/javascript" charset="utf-8">
var elfinder_root_dir = '<?=$elfinder_root_dir;?>';

$().ready(function() {
   var keyField = "field";
   var valueField = getQuerystring(keyField, null);

if(valueField == "imagemanager") {
    $('#finder').elfinder({
        url: '<?= ELFINDER_URL;?>php/connector.php?field=imagemanager',
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
        url: '<?= ELFINDER_URL;?>php/connector.php',
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
})
</script>

</head>
<body>	
		<div id="finder"></div>	
</body>
</html>
