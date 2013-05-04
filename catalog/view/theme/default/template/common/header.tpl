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
<link rel="icon" type="image/ico" href="<?= $icon;?>"></link> 
<link rel="shortcut icon" href="<?= $icon;?>"></link>
<? } ?>
<? if($canonical_link) {?>
<link href="<?= $canonical_link;?>" rel="canonical" />
<? }?>
	<!--[if lt IE 10]>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie_style.css" />
<![endif]-->
	<? foreach ($css_styles as $style) { ?>
<link rel="<?= $style['rel']; ?>" type="text/css" href="<?= $style['href']; ?>" media="<?= $style['media']; ?>" />
<? } ?>
<script type="text/javascript" src="catalog/view/javascript/jquery/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
<script type="text/javascript" src="catalog/view/javascript/jquery/ui/external/jquery.cookie.js"></script>
<script type="text/javascript" src="catalog/view/javascript/jquery/colorbox/jquery.colorbox.js"></script>
<link rel="stylesheet" type="text/css" href="catalog/view/javascript/jquery/colorbox/colorbox.css" media="screen" />
<script type="text/javascript" src="catalog/view/javascript/jquery/tabs.js"></script>
<script type="text/javascript" src="catalog/view/javascript/common.js"></script>

<? /*TODO: Do we want to do something with this? Right now useles...	
<![if !IE]>
<script type="text/javascript">//<!--
//url_state_object = {};
//window.history.pushState(url_state_object,'<?= addslashes($title);?>', '<?=isset($pretty_url) ? $pretty_url : '';?>');
//--></script>
<![endif]>
 */ ?>
 
<? foreach ($js_scripts as $script) { ?>
<script type="text/javascript" src="<?= $script; ?>"></script>
<? } ?>
<!--[if IE 9]>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie9.css" />
<![endif]-->
	<!--[if IE 8]>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie8.css" />
<![endif]-->
<!--[if IE 7]>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie7.css" />
<![endif]-->
<!--[if lt IE 7]>
<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/stylesheet/ie6.css" />
<script type="text/javascript" src="catalog/view/javascript/DD_belatedPNG_0.0.8a-min.js"></script>
<script type="text/javascript">
DD_belatedPNG.fix('#logo img');
</script>
<![endif]-->

<?= $google_analytics; ?>
<?= $statcounter;?>
</head>
<body>
<div id="container">
<div id='container_content'>
<div id="header">
	<? if ($logo) { ?>
	<div id="logo" class="<?= $logo;?>">
		<a href="<?= $home; ?>">
			<img src="<?= $logo; ?>" title="<?= $name; ?>" alt="<?= $name; ?>" />
			<div id="slogan"><?= $text_slogan;?></div>
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
					<span><?= $block_login;?></span>
				<? } else {?>
					<span><?= $text_login_link;?></span>
				<? } ?>
			<? } else { ?>
				<? $this->builder->set_config("href", "display") ;?>
				<?=$this->builder->build('select', $links_account, 'account_menu', '', array('onchange' => "window.location = $(this).val()"));?>
			<? } ?>
		</div>
		<? if(!empty($social_networks)){?>
			<div id="header_social_networks">
				<?= $social_networks;?>
			</div>
		<? } ?>
	</div>
	
	<? if(!empty($links_secondary)){?>
	<div id="links_secondary" class="links">
		 <?= $this->builder->build_links($links_secondary);?>
	</div>
	<? }?>
	<? if(!empty($links_primary)) { ?>
	<div id="links_primary" class="links">
		<?= $this->builder->build_links($links_primary);?>
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
<div id="content-holder">
<?=$this->builder->display_messages($messages);?>
<?= $above_content; ?>

<script type="text/javascript">//<!--
$('#links_primary .top_menu > li').hover(top_menu_hoverin, top_menu_hoverout);
function top_menu_hoverin(){
	$(this).addClass('active')
	if($(this).find('ul').children().length){
		$(this).append("<div class='submenu_arrow'></div>");
	}
}
function top_menu_hoverout(){
	$(this).removeClass('active')
		.find('.submenu_arrow').remove();
}
//--></script>

