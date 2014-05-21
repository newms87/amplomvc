<?= call('admin/common/header'); ?>
<div class="section">
	<?= breadcrumbs(); ?>
	<? if ($error_warning) { ?>
		<div class="message warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/review.png'); ?>" alt=""/> <?= _l("Reviews"); ?></h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a><a
					href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Author:"); ?></td>
						<td><input type="text" name="author" value="<?= $author; ?>"/>
							<? if (_l("Author must be between 3 and 64 characters!")) { ?>
								<span class="error"><?= _l("Author must be between 3 and 64 characters!"); ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= _l("Product:<br/><span class=\"help\">(Autocomplete)</span>"); ?></td>
						<td><input type="text" name="product" value="<?= $subscription; ?>"/>
							<input type="hidden" name="product_id" value="<?= $product_id; ?>"/>
							<? if (_l("Product required!")) { ?>
								<span class="error"><?= _l("Product required!"); ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Text:"); ?></td>
						<td><textarea name="text" cols="60" rows="8"><?= $text; ?></textarea>
							<? if (_l("Review Text must be at least 1 character!")) { ?>
								<span class="error"><?= _l("Review Text must be at least 1 character!"); ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= _l("Rating:"); ?></td>
						<td><b class="rating"><?= _l("Bad"); ?></b>&nbsp;
							<? if ($rating == 1) { ?>
								<input type="radio" name="rating" value="1" checked/>
							<? } else { ?>
								<input type="radio" name="rating" value="1"/>
							<? } ?>
							&nbsp;
							<? if ($rating == 2) { ?>
								<input type="radio" name="rating" value="2" checked/>
							<? } else { ?>
								<input type="radio" name="rating" value="2"/>
							<? } ?>
							&nbsp;
							<? if ($rating == 3) { ?>
								<input type="radio" name="rating" value="3" checked/>
							<? } else { ?>
								<input type="radio" name="rating" value="3"/>
							<? } ?>
							&nbsp;
							<? if ($rating == 4) { ?>
								<input type="radio" name="rating" value="4" checked/>
							<? } else { ?>
								<input type="radio" name="rating" value="4"/>
							<? } ?>
							&nbsp;
							<? if ($rating == 5) { ?>
								<input type="radio" name="rating" value="5" checked/>
							<? } else { ?>
								<input type="radio" name="rating" value="5"/>
							<? } ?>
							&nbsp; <b class="rating"><?= _l("Good"); ?></b>
							<? if (_l("Review rating required!")) { ?>
								<span class="error"><?= _l("Review rating required!"); ?></span>
							<? } ?></td>
					</tr>
					<tr>
						<td><?= _l("Status:"); ?></td>
						<td><select name="status">
								<? if ($status) { ?>
									<option value="1" selected="selected"><?= _l("Enabled"); ?></option>
									<option value="0"><?= _l("Disabled"); ?></option>
								<? } else { ?>
									<option value="1"><?= _l("Enabled"); ?></option>
									<option value="0" selected="selected"><?= _l("Disabled"); ?></option>
								<? } ?>
							</select></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript"><
	!--
		$('input[name=\'product\']').autocomplete({
			delay: 0,
			source: function (request, response) {
				$.ajax({
					url: "<?= $url_product_autocomplete; ?>" + '&filter_name=" + encodeURIComponent(request.term),
					dataType: "json',
					success: function (json) {
						response($.map(json, function (item) {
							return {
								label: item.name,
								value: item.product_id
							}
						}));
					}
				});
			},
			select: function (event, ui) {
				$('input[name=\'product\']').val(ui.item.label);
				$('input[name=\'product_id\']').val(ui.item.value);

				return false;
			}
		});
</script>
<?= call('admin/common/footer'); ?>
