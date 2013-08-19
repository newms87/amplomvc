<div class='share_block' class="share">
	<div class='share_block_loading'><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>"
	                                      alt=""/><?= $text_share_loading; ?></div>
	<div class="addthis_toolbox addthis_default_style share_block_content" style='display:none'>
		<a class="addthis_button_pinterest_pinit"></a>
		<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
		<a class="addthis_button_tweet"></a>
		<a class="addthis_button_tumblr"></a>
		<a class="addthis_counter addthis_pill_style"></a>
	</div>
</div>

<script type="text/javascript">//<!--
	$(document).ready(function () {
		$('.addthis_button_tumblr').append("<img src='<?= HTTPS_IMAGE . 'data/tumblr_pill.png'; ?>' />");
		$.getScript("http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4febc0f66808d769",
			function () {
				show_social_media_sharing();
			});
	});

	function show_social_media_sharing() {
		show = true;
		$('.share_block_content > a').not('.addthis_button_tumblr').each(function (i, e) {
			if (!$(e).html()) {
				show = false;
				return false;
			}
		});

		if (show) {
			$('.share_block_loading').remove();
			$('.share_block_content').fadeIn(500);
		}
		else {
			setTimeout(show_social_media_sharing, 200);
		}
	}
//--></script>