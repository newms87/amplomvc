<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<? if ($error_warning) { ?>
			<div class="message_box warning"><?= $error_warning; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'review.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a
						href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_author; ?></td>
							<td><input type="text" name="author" value="<?= $author; ?>"/>
								<? if ($error_author) { ?>
									<span class="error"><?= $error_author; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= $entry_product; ?></td>
							<td><input type="text" name="product" value="<?= $product; ?>"/>
								<input type="hidden" name="product_id" value="<?= $product_id; ?>"/>
								<? if ($error_product) { ?>
									<span class="error"><?= $error_product; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_text; ?></td>
							<td><textarea name="text" cols="60" rows="8"><?= $text; ?></textarea>
								<? if ($error_text) { ?>
									<span class="error"><?= $error_text; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= $entry_rating; ?></td>
							<td><b class="rating"><?= $entry_bad; ?></b>&nbsp;
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
								&nbsp; <b class="rating"><?= $entry_good; ?></b>
								<? if ($error_rating) { ?>
									<span class="error"><?= $error_rating; ?></span>
								<? } ?></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><select name="status">
									<? if ($status) { ?>
										<option value="1" selected="selected"><?= $text_enabled; ?></option>
										<option value="0"><?= $text_disabled; ?></option>
									<? } else { ?>
										<option value="1"><?= $text_enabled; ?></option>
										<option value="0" selected="selected"><?= $text_disabled; ?></option>
									<? } ?>
								</select></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript"><!--
		$('input[name=\'product\']').autocomplete({
			delay: 0,
			source: function (request, response) {
				$.ajax({
					url: "<?= $url_product_autocomplete; ?>" + '&filter_name=' + encodeURIComponent(request.term),
					dataType: 'json',
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
//--></script>
<?= $footer; ?>