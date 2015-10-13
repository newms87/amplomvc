<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?= $title; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=320, target-densitydpi=device-dpi">
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #000000;">
<div style="width: 680px;text-align: center; margin: auto;">
	<? if ($logo) { ?>
		<div style="margin-bottom: 10px;">
			<a href="<?= site_url(); ?>" title="<?= option('site_name'); ?>">
				<?= img($logo, array(
					'width'  => option('site_email_logo_width', 400),
					'height' => option('site_email_logo_height', 150),
					'#title' => option('site_name'),
					'#alt'   => option('site_name'),
					'#style' => "margin-bottom: 20px; border: none;",
				)); ?>
			</a>
		</div>
	<? } ?>
