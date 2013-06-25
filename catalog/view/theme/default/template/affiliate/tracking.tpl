<?= $header; ?><?= $column_left; ?><?= $column_right; ?>
<div id="content"><?= $content_top; ?>
	<?= $breadcrumbs; ?>
	<h1><?= $heading_title; ?></h1>
	<p><?= $text_description; ?></p>
	<p><?= $text_code; ?><br />
		<textarea cols="40" rows="5"><?= $code; ?></textarea>
	</p>
	<p><?= $text_generator; ?><br />
		<input type="text" name="product" value="" />
	</p>
	<p><?= $text_link; ?><br />
		<textarea name="link" cols="40" rows="5"></textarea>
	</p>
	<div class="buttons">
		<div class="right"><a href="<?= $continue; ?>" class="button"><?= $button_continue; ?></a></div>
	</div>
	<?= $content_bottom; ?></div>
<script type="text/javascript">
//<!--
$('input[name=\'product\']').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: "<?= HTTP_CATALOG . "index.php?route=affiliate/tracking/autocomplete"; ?>" + '&filter_name=' +	encodeURIComponent(request.term),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item.name,
						value: item.link
					}
				}));
			}
		});
	},
	select: function(event, ui) {
		$('input[name=\'product\']').attr('value', ui.item.label);
		$('textarea[name=\'link\']').attr('value', ui.item.value);
						
		return false;
	}
});
//--></script>
<?= $footer; ?>