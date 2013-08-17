<? if ($icon_size == 'tiny') { ?>
	<a class="janrainEngage janiainengage_a janrain_tiny" href="#" onclick="return false;">
		<? foreach ($display_icons as $icon) { ?>
			<div class="janrain_icon_<?= $icon_size; ?>"
			     style="background: url(<?= HTTPS_IMAGE . "janrain/rpx-icons$image_size.png"; ?>) no-repeat 0 <?= $image_offset[$icon] * -$image_size; ?>px"></div>
		<? } ?>
	</a>

<? } else { ?>
	<div class="box janrain_signin">
		<div class="box_heading"><?= $head_title; ?></div>
		<div id="janrain" class="box_content">
			<div class="janiainengage janrain_login">
				<? if ($display_type == 'iframe') { ?>
					<div id="janrainEngageEmbed"></div>
				<? } else { ?>
					<a class="janrainEngage janiainengage_a janrain_icon_list" href="#" onclick="return false;">
						<? foreach ($display_icons as $icon) { ?>
							<div class="janrain_icon_<?= $icon_size; ?>"
							     style="background: url(<?= HTTPS_IMAGE . "janrain/rpx-icons$image_size.png"; ?>) no-repeat 0 <?= $image_offset[$icon] * -$image_size; ?>px"></div>
						<? } ?>
					</a>
				<? } ?>
			</div>
		</div>
	</div>
<? } ?>

<script type="text/javascript">//<!--
	window.janrain = {};
	window.janrain.settings = {};

	janrain.settings = {};
	janrain.settings.tokenUrl = '<?= $janrain_token_url; ?>';
	janrain.settings.type = '<?= $display_type=='iframe' ? 'embed' : 'modal';; ?>';
	janrain.settings.language = '<?= $janrain_lang; ?>';
	janrain.settings.showAttribution = false;
	janrain.ready = true;

	if (document.location.protocol === 'https:') {
		src = 'https://rpxnow.com/js/lib/<?= $application_domain; ?>/engage.js';
	} else {
		src = 'http://widget-cdn.rpxnow.com/js/lib/<?= $application_domain; ?>/engage.js';
	}

	$(document).ready(function () {
		$.getScript(src);
	});
	//--></script>
