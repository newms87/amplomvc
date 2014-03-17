<!DOCTYPE html>
<? if (isset($_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) {
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
} ?>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?= $direction; ?>" lang="<?= $lang; ?>" xml:lang="<?= $lang; ?>">
	<head>
		<title><?= $title; ?></title>

		<?= $styles; ?>
		<?= $scripts; ?>

		<script type="text/javascript">
			if (Function('/*@cc_on return document.documentMode===10@*/')()) {
				document.documentElement.className += ' IE10';
			}
			else if (Function('/*@cc_on return document.documentMode===9@*/')()) {
				document.documentElement.className += ' IE9';
			}
			else if (Function('/*@cc_on return document.documentMode===8@*/')()) {
				document.documentElement.className += ' IE8';
			}
			else if (Function('/*@cc_on return document.documentMode===7@*/')()) {
				document.documentElement.className += ' IE7';
			}
		</script>

		<!--[if IE 9]>
		<link rel="stylesheet" type="text/css" href="<?= URL_THEME . "style/ ie9.css"; ?>" />
		<![endif]-->
		<!--[if IE 8]>
		<link rel="stylesheet" type="text/css" href="<?= URL_THEME . "style/ ie8.css"; ?>" />
		<![endif]-->
		<!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="<?= URL_THEME . "style/ ie7.css"; ?>" />
		<![endif]-->
	</head>

	<body class="page_preview">
		<div id="container" style="margin-left: -60px">
			<div id="page">
				<div id="content_holder">

					<div class="content">
						<style id="page_css"><?= $css; ?></style>

						<div class="section">
							<h1 id="page_title" <?= empty($display_title) ? 'style="display:none"' : ''; ?>><?= $title; ?></h1>

							<div class="page_content"><?= $content; ?></div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</body>
</html>

<script type="text/javascript">
	$('#container').draggable();
</script>
