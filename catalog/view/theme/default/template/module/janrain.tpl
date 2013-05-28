<style>
#janrain{
	overflow:auto;
}
#janrainModal{
	top: 25%!important;
}
.janrain_login{
	text-align:center;
}
.janrain_icon_list,.janrain_icon_list:hover{text-decoration:none;background:none;}

.janrain_icon_list div{
	float:left;
	margin-left:5px;
}

.janrain_tiny{
	display:inline-block;
}
.janrain_tiny div{
	float:left;
	margin-left:5px;
}
.janrain_icon_tiny{
	background: url(<?= HTTPS_IMAGE;?>/data/rpx-icons16.png) no-repeat top left;
	width:16px;
	height:16px;
}

.janrain_icon_small{
	background: url(<?= HTTPS_IMAGE;?>/data/rpx-icons16.png) no-repeat top left;
	width:16px;
	height:16px;
}

.janrain_icon_large{
	background: url(<?= HTTPS_IMAGE;?>/data/rpx-icons30.png) no-repeat top left;
	width:30px;
	height:30px;
}
</style>

<? if($icon_size == 'tiny'){?>
	<a class="janrainEngage janiainengage_a janrain_tiny" href="#" onclick="return false;">
			<? foreach($janrain_display_icons as $icon){?>
				<div class="janrain_icon_<?=$icon_size;?>" style="background-position:0 <?=$image_offset[$icon] * -$image_size;?>px"></div>
			<? }?>
	</a>
<? }else{?>
<div class="box">
	<div class="box-heading"><?= $heading_title; ?></div>
	<div id="janrain" class="box-content">
			<div class="janiainengage janrain_login">
			<? if( $display_type == 'iframe') {?>
				<div id="janrainEngageEmbed"></div>
			<? } else { ?>
				<a class="janrainEngage janiainengage_a janrain_icon_list" href="#" onclick="return false;">
						<? foreach($janrain_display_icons as $icon){?>
							<div class="janrain_icon_<?=$icon_size;?>" style="background-position:0 <?=$image_offset[$icon] * -$image_size;?>px"></div>
						<? }?>
				</a>
			<? }?>
			</div>
	</div>
</div>
<? }?>

<script type="text/javascript">//<!--
	window.janrain = {};
	window.janrain.settings = {};

	janrain.settings={};
	janrain.settings.tokenUrl='<?= $janrain_token_url;?>';
	janrain.settings.type='<?= $display_type=='iframe' ? 'embed' : 'modal';;?>';
	janrain.settings.language='<?= $janrain_lang;?>';
	janrain.settings.showAttribution=false;
	janrain.ready=true;

	if(document.location.protocol === 'https:'){
		src='https://rpxnow.com/js/lib/<?= $janrain_application_domain;?>/engage.js';
	}
	else{
		src='http://widget-cdn.rpxnow.com/js/lib/<?= $janrain_application_domain;?>/engage.js';
	}

	$(document).ready(function(){
		$.getScript(src);
	});
//--></script>
