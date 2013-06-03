<?= '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
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
<link rel="stylesheet" type="text/css" href="view/stylesheet/stylesheet.css" />
<? foreach ($css_styles as $style) { ?>
<link rel="<?= $style['rel']; ?>" type="text/css" href="<?= $style['href']; ?>" media="<?= $style['media']; ?>" />
<? } ?>
<script type="text/javascript" src="view/javascript/jquery/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui-1.8.16.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="view/javascript/jquery/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css" />
<script type="text/javascript" src="view/javascript/jquery/ui/external/jquery.bgiframe-2.1.2.js"></script>
<script type="text/javascript" src="view/javascript/jquery/tabs.js"></script>
<script type="text/javascript" src="view/javascript/jquery/superfish/js/superfish.js"></script>

<![if !IE]>
<script type="text/javascript">//<!--
url_state_object = {};
window.history.pushState(url_state_object,'<?= $title; ?>', '<?= isset($pretty_url) ? $pretty_url : ''; ?>');
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
		<div class="div2"><img src="<?= $admin_logo; ?>" title="<?= $heading_title; ?>" onclick="location = '<?= $home; ?>'" /></div>
		<? if ($logged) { ?>
		<div class="div3"><img src="<?= HTTP_THEME_IMAGE . 'lock.png'; ?>" alt="" style="position: relative; top: 3px;" />&nbsp;<?= $logged; ?></div>
		<div class="div3" style="clear:right"><?= $support; ?></div>
		<? } ?>
	</div>
	<? if ($logged) { ?>
	<div id="menu">
		<ul class="left" style="display: none;">
			<li id="Products">
				<a href="<?= $product; ?>" class="top"><?= $text_product; ?></a>
				<ul>
					<li><a href="<?= $product_insert; ?>"><?= $text_product_insert; ?></a></li>
					<li><a href="<?= $product; ?>"><?= $text_product; ?></a></li>
				</ul>
			</li>
			<li id='Designers'>
				<a href='<?= $designers; ?>' class='top'><?= $text_designer_info; ?></a>
			</li>
			<li id='User'>
				<a href='<?= $user_info; ?>' class='top'><?= $text_user_info; ?></a>
			</li>
		</ul>
		<ul class='right'>
			<li id="store"><a class="top" href="<?= $logout; ?>"><?= $text_logout; ?></a></li>
		</ul>
		<script type="text/javascript"><!--
$(document).ready(function() {
	$('#menu > ul').superfish({
		hoverClass	: 'sfHover',
		pathClass	: 'overideThisToUse',
		delay		: 0,
		animation	: {height: 'show'},
		speed		: 'normal',
		autoArrows	: false,
		dropShadows	: false,
		disableHI	: false, /* set to true to disable hoverIntent detection */
		onInit		: function(){},
		onBeforeShow : function(){},
		onShow		: function(){},
		onHide		: function(){}
	});
	
	$('#menu > ul').css('display', 'block');
});
 
function getURLVar(urlVarName) {
	var urlHalves = String(document.location).toLowerCase().split('?');
	var urlVarValue = '';
	
	if (urlHalves[1]) {
		var urlVars = urlHalves[1].split('&');

		for (var i = 0; i <= (urlVars.length); i++) {
			if (urlVars[i]) {
				var urlVarPair = urlVars[i].split('=');
				
				if (urlVarPair[0] && urlVarPair[0] == urlVarName.toLowerCase()) {
					urlVarValue = urlVarPair[1];
				}
			}
		}
	}
	
	return urlVarValue;
}

$(document).ready(function() {
	route = getURLVar('route');
	
	if (!route) {
		$('#dashboard').addClass('selected');
	} else {
		part = route.split('/');
		
		url = part[0];
		
		if (part[1]) {
			url += '/' + part[1];
		}
		
		$('a[href*=\'' + url + '\']').parents('li[id]').addClass('selected');
	}
});
//--></script>
	</div>
	<? } ?>
</div>

<div id='content'>
<?= $this->builder->display_messages($messages); ?>