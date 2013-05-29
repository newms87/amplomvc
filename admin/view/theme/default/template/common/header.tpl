<?= '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?= $direction; ?>" lang="<?= $lang; ?>" xml:lang="<?= $lang; ?>">
<head>
<title><?= $title; ?></title>
<base href="<?= $base; ?>" />
<? if ($description) { ?>
<meta name="description" content="<?= $description; ?>" />
<? } ?>
<? if ($keywords) { ?>
<meta name="keywords" content="<?= $keywords; ?>" />
<? } ?>
<? if($canonical_link) {?>
<link href="<?= $canonical_link; ?>" rel="canonical" />
<? }?>

<? foreach ($css_styles as $style) { ?>
<link rel="<?= $style['rel']; ?>" type="text/css" href="<?= $style['href']; ?>" media="<?= $style['media']; ?>" />
<? } ?>

<![if !IE]>
<script type="text/javascript">//<!--
url_state_object = {};
window.history.pushState(url_state_object,'<?= addslashes($title); ?>', '<?= isset($pretty_url) ? $pretty_url : ''; ?>');
//--></script>
<![endif]>

<? foreach ($js_scripts as $script) { ?>
<script type="text/javascript" src="<?= $script; ?>"></script>
<? } ?>

</head>
<body>
<div id="container">
<div id="header">
	<div class="div1">
		<div class="div2"><a href="<?= $home; ?>" style="display:block"><img src="<?= $admin_logo; ?>" title="<?= $heading_title; ?>" /></a></div>
		<? if ($logged) { ?>
		<div class="div3"><img src="<?= HTTP_THEME_IMAGE . 'lock.png'; ?>" alt="" id="header_secure_lock" /><?= $logged; ?></div>
		<div class="div3" style="clear:right"><?= $support; ?></div>
		<? } ?>
	</div>
	<? if ($logged) { ?>
	<div id="menu" class="links">
	<div class="left"><?= $this->builder->build_links($links_admin); ?></div>
		<div class="right"><?= $this->builder->build_links($links_right); ?></div>
		<div class="clear"></div>
	</div>
	<? } ?>
</div>

<div id='content'>
<?= $this->builder->display_messages($messages); ?>

<?= $this->builder->js('image_manager'); ?>

