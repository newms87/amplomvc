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
<link href="<?= $canonical_link;?>" rel="canonical" />
<? }?>
<link rel="stylesheet" type="text/css" href="view/stylesheet/stylesheet.css" />
<? foreach ($css_styles as $style) { ?>
<link rel="<?= $style['rel']; ?>" type="text/css" href="<?= $style['href']; ?>" media="<?= $style['media']; ?>" />
<? } ?>
<script type="text/javascript" src="view/javascript/jquery/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui-1.9.2.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="view/javascript/jquery/ui/external/jquery.bgiframe-2.1.2.js"></script>
<script type="text/javascript" src="view/javascript/jquery/tabs.js"></script>
<script type="text/javascript" src="view/javascript/common.js"></script>

<![if !IE]>
<script type="text/javascript">//<!--
url_state_object = {};
window.history.pushState(url_state_object,'<?=addslashes($title);?>', '<?=isset($pretty_url) ? $pretty_url : '';?>');
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
    <div class="div2"><a href="<?= $home;?>" style="display:block"><img src="<?=$admin_logo;?>" title="<?= $heading_title; ?>" /></a></div>
    <? if ($logged) { ?>
    <div class="div3"><img src="view/image/lock.png" alt="" style="position: relative; top: 3px;" />&nbsp;<?= $logged; ?></div>
    <div class="div3" style="clear:right"><?= $support;?></div>
    <? } ?>
  </div>
  <? if ($logged) { ?>
  <div id="menu" class="links">
	 <div class="left"><?= $this->builder->build_links($links_admin);?></div>
  	 <div class="right"><?= $this->builder->build_links($links_right);?></div>
  </div>
  <? } ?>
</div>

<div id='content'>
<?=$this->builder->display_messages($messages);?>
