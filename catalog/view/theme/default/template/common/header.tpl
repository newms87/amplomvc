<!DOCTYPE html>
<? if (isset($_SERVER['HTTP_USER_AGENT']) && !strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE 6')) echo '<?xml version="1.0" encoding="UTF-8"?>'. "\n"; ?>
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
<? if ($icon) { ?>
<link rel="icon" type="image/ico" href="<?= $icon; ?>"></link>
<link rel="shortcut icon" href="<?= $icon; ?>"></link>
<? } ?>
<? if($canonical_link) {?>
<link href="<?= $canonical_link; ?>" rel="canonical" />
<? }?>

<? foreach ($css_styles as $style) { ?>
<link rel="<?= $style['rel']; ?>" type="text/css" href="<?= $style['href']; ?>" media="<?= $style['media']; ?>" />
<? } ?>

<? /*TODO: Do we want to do something with this? Right now useles...
<![if !IE]>
<script type="text/javascript">//<!--
//url_state_object = {};
//window.history.pushState(url_state_object,'<?= addslashes($title); ?>', '<?= isset($pretty_url) ? $pretty_url : ''; ?>');
//--></script>
<![endif]>
 */ ?>
 
<? foreach ($js_scripts as $script) { ?>
<script type="text/javascript" src="<?= $script; ?>"></script>
<? } ?>

<script type="text/javascript">//<!--
if (Function('/*@cc_on return document.documentMode===10@*/')()){
    document.documentElement.className+=' IE10';
}
else if (Function('/*@cc_on return document.documentMode===9@*/')()){
    document.documentElement.className+=' IE9';
}
else if (Function('/*@cc_on return document.documentMode===8@*/')()){
    document.documentElement.className+=' IE8';
}
else if (Function('/*@cc_on return document.documentMode===7@*/')()){
    document.documentElement.className+=' IE7';
}
//--></script>

<!--[if IE 9]>
<link rel="stylesheet" type="text/css" href="<?= HTTP_THEME_STYLE . "ie9.css"; ?>" />
<![endif]-->
	<!--[if IE 8]>
<link rel="stylesheet" type="text/css" href="<?= HTTP_THEME_STYLE . "ie8.css"; ?>" />
<![endif]-->
<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="<?= HTTP_THEME_STYLE . "ie7.css"; ?>" />
<![endif]-->

<? if($google_analytics) { ?>
<!-- Google Analytics Tracker -->
<script type="text/javascript">//<!--
<?= $google_analytics; ?>
//--></script>
<? } ?>

<? if(!empty($statcounter)) { ?>
<!-- Stat Counter Tracker -->
<script type="text/javascript">//<!--
var sc_project="<?= $statcounter['project']; ?>";
var sc_invisible=1;
var sc_security="<?= $statcounter['security']; ?>";

$(document).ready(function(){
	$.getScript('http://www.statcounter.com/counter/counter.js');
});
//--></script>
<noscript>
	<div class="statcounter">
		<a title="tumblr tracker" href="http://statcounter.com/tumblr/" target="_blank">
			<img class="statcounter" src="http://c.statcounter.com/<?= $statcounter['project']; ?>/0/<?= $statcounter['security']; ?>/1/" alt="tumblr tracker">
		</a>
	</div>
</noscript>
<? } ?>

</head>
<body>
<div id="container">
<div id='container_content'>
<div id="header">
	<? if ($logo) { ?>
	<div id="logo" class="<?= $logo; ?>">
		<a href="<?= $home; ?>">
			<img src="<?= $logo; ?>" title="<?= $name; ?>" alt="<?= $name; ?>" />
			<div id="slogan"><?= $text_slogan; ?></div>
		</a>
	</div>
	<? } ?>
	<? if(!empty($page_header)){
		echo "<div id='the_page_header'>$page_header</div>";
	}?>
	<?= $language; ?>
	<?= $currency; ?>
	
	<div id="header_right">
		<div id="links_account">
			<? if(!$is_logged){ ?>
				<? if(!empty($block_login)){?>
					<span><?= $block_login; ?></span>
				<? } else {?>
					<span><?= $text_login_link; ?></span>
				<? } ?>
			<? } else { ?>
				<? $this->builder->set_config("href", "display_name") ;?>
				<?= $this->builder->build('select', $links_account, 'account_menu', '', array('onchange' => "window.location = $(this).val()")); ?>
			<? } ?>
		</div>
		
		<? if (!empty($links_cart)) { ?>
			<div id="links_cart">
				<?= $this->builder->build_links($links_cart); ?>
			</div>
		<? } ?>
		
		<? if(!empty($social_networks)){?>
			<div id="header_social_networks">
				<?= $social_networks; ?>
			</div>
		<? } ?>
	</div>
	
	<? if(!empty($links_secondary)){?>
	<div id="links_secondary" class="links">
		<?= $this->builder->build_links($links_secondary); ?>
	</div>
	<? }?>
	<? if(!empty($links_primary)) { ?>
	<div id="links_primary" class="links">
		<?= $this->builder->build_links($links_primary); ?>
	</div>
	<? } ?>
</div>
<? if (isset($categories) && !empty($categories)) { ?>
<div id="menu">
	<ul>
		<? foreach ($categories as $category) { ?>
		<li><a href="<?= $category['href']; ?>"><?= $category['name']; ?></a>
				<? if ($category['children']) { ?>
				<div>
					<? for ($i = 0; $i < count($category['children']);) { ?>
					<ul>
						<? $j = $i + ceil(count($category['children']) / $category['column']); ?>
						<? for (; $i < $j; $i++) { ?>
						<? if (isset($category['children'][$i])) { ?>
						<li><a href="<?= $category['children'][$i]['href']; ?>"><?= $category['children'][$i]['name']; ?></a></li>
						<? } ?>
						<? } ?>
					</ul>
					<? } ?>
				</div>
				<? } ?>
		</li>
		<? } ?>
	</ul>
</div>
<? } ?>
<div id="notification"></div>
<div id="content_holder">
<?= $this->builder->display_messages($messages); ?>
<?= $above_content; ?>

<script type="text/javascript">//<!--
$('#links_primary .top_menu > li').hover(top_menu_hoverin, top_menu_hoverout);
function top_menu_hoverin(){
	$(this).addClass('hover')
	if($(this).find('ul').children().length){
		$(this).append("<div class='submenu_arrow'></div>");
	}
}
function top_menu_hoverout(){
	$(this).removeClass('hover')
		.find('.submenu_arrow').remove();
}
//--></script>

