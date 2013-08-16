<?= '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?= $direction; ?>" lang="<?= $language; ?>"
      xml:lang="<?= $language; ?>">
<head>
	<title><?= $title; ?></title>
	<base href="<?= $base; ?>"/>
</head>
<body>
<div style="text-align: center;">
	<h1><?= $head_title; ?></h1>

	<p><?= $text_response; ?></p>

	<div style="border: 1px solid #DDDDDD; margin-bottom: 20px; width: 350px; margin-left: auto; margin-right: auto;">
		<WPDISPLAY ITEM=banner>
	</div>
	<p><?= $text_failure; ?></p>

	<p><?= $text_failure_wait; ?></p>
</div>
<script type="text/javascript">
	//<!--
	setTimeout('location = \'<?= $continue; ?>\';', 2500);
	//--></script>
</body>
</html>