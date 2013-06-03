<div id="product_additional_tabs" class="htabs">
	<? if($description){?>
	<a href="#tab-description"><?= $tab_description; ?></a>
	<? }?>
	<a href="#tab-shipping"><?= $tab_shipping; ?></a>
	<? if (!empty($attribute_groups)) { ?>
	<a href="#tab-attribute"><?= $tab_attribute; ?></a>
	<? } ?>
	<? if ($review_status) { ?>
	<a href="#tab-review"><?= $tab_review; ?></a>
	<? } ?>
</div>
	<? if($description){?>
<div id="tab-description" class="tab-content"><?= $description; ?></div>
	<? }?>
<div id="tab-shipping" class="tab-content">
	<?= $shipping_return; ?><br />
	<?= $is_final?$final_sale_explanation:''; ?><br /><br /><br />
	<?= $shipping_return_link; ?>
</div>
<? if (!empty($attribute_groups)) { ?>
<div id="tab-attribute" class="tab-content">
	<table class="attribute">
		<? foreach ($attribute_groups as $attribute_group) { ?>
		<thead>
			<tr>
				<td colspan="2"><?= $attribute_group['name']; ?></td>
			</tr>
		</thead>
		<tbody>
			<? foreach ($attribute_group['attribute'] as $attribute) { ?>
			<tr>
				<td><?= $attribute['name']; ?></td>
				<td><?= $attribute['text']; ?></td>
			</tr>
			<? } ?>
		</tbody>
		<? } ?>
	</table>
</div>
<? } ?>
<? if ($review_status) { ?>
<div id="tab-review" class="tab-content">
	<div id="review"></div>
	<h2 id="review-title"><?= $text_write; ?></h2>
	<b><?= $entry_name; ?></b><br />
	<input type="text" name="name" value="" />
	<br />
	<br />
	<b><?= $entry_review; ?></b>
	<textarea name="text" cols="40" rows="8" style="width: 98%;"></textarea>
	<span style="font-size: 11px;"><?= $text_note; ?></span><br />
	<br />
	<b><?= $entry_rating; ?></b> <span><?= $entry_bad; ?></span>&nbsp;
	<input type="radio" name="rating" value="1" />
	&nbsp;
	<input type="radio" name="rating" value="2" />
	&nbsp;
	<input type="radio" name="rating" value="3" />
	&nbsp;
	<input type="radio" name="rating" value="4" />
	&nbsp;
	<input type="radio" name="rating" value="5" />
	&nbsp; <span><?= $entry_good; ?></span><br />
	<br />
	<b><?= $entry_captcha; ?></b><br />
	<input type="text" name="captcha" value="" />
	<br />
	<img src="index.php?route=product/product/captcha" alt="" id="captcha" /><br />
	<br />
	<div class="buttons">
		<div class="right"><a id="button-review" class="button"><?= $button_continue; ?></a></div>
	</div>
</div>
<? } ?>

<script type="text/javascript">//<!--
$('#review .pagination a').live('click', function() {
	$('#review').slideUp('slow');
			
	$('#review').load(this.href);
	
	$('#review').slideDown('slow');
	
	return false;
});

$('#review').load("<?= HTTP_CATALOG . "index.php?route=product/product/review"; ?>" + '&product_id=<?= $product_id; ?>');

$('#button-review').bind('click', function() {
	$.ajax({
			url: "<?= HTTP_CATALOG . "index.php?route=product/product/write"; ?>" + '&product_id=<?= $product_id; ?>',
			type: 'post',
			dataType: 'json',
			data: 'name=' + encodeURIComponent($('input[name=\'name\']').val()) + '&text=' + encodeURIComponent($('textarea[name=\'text\']').val()) + '&rating=' + encodeURIComponent($('input[name=\'rating\']:checked').val() ? $('input[name=\'rating\']:checked').val() : '') + '&captcha=' + encodeURIComponent($('input[name=\'captcha\']').val()),
			beforeSend: function() {
				$('.success, .warning').remove();
				$('#button-review').attr('disabled', true);
				$('#review-title').after('<div class="attention"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> <?= $text_wait; ?></div>');
			},
			complete: function() {
				$('#button-review').attr('disabled', false);
				$('.attention').remove();
			},
			success: function(data) {
				if (data.error) {
						$('#review-title').after('<div class="message_box warning">' + data.error + '</div>');
				}
				
				if (data.success) {
						$('#review-title').after('<div class="message_box success">' + data.success + '</div>');
												
						$('input[name=\'name\']').val('');
						$('textarea[name=\'text\']').val('');
						$('input[name=\'rating\']:checked').attr('checked', '');
						$('input[name=\'captcha\']').val('');
				}
			}
	});
});
//--></script>

<script type="text/javascript">//<!--
$('#product_additional_tabs a').tabs();
//--></script>