<? if ($reviews) { ?>
	<? foreach ($reviews as $review) { ?>
		<div class="section"><b><?= $review['author']; ?></b> | <img
				src="<?= HTTP_THEME_IMAGE . "stars-$review[rating].png"; ?>" alt="<?= $review['reviews']; ?>"/><br/>
			<?= $review['date_added']; ?><br/>
			<br/>
			<?= $review['text']; ?></div>
	<? } ?>
	<div class="pagination"><?= $pagination; ?></div>
<? } else { ?>
	<div class="section"><?= $text_no_reviews; ?></div>
<? } ?>

<? if ($review_status) { ?>
	<div id="tab-review" class="tab-content">
		<div id="review"></div>
		<h2 id="review-title"><?= $text_write; ?></h2>
		<b><?= _l("Name"); ?></b><br/>
		<input type="text" name="name" value=""/>
		<br/>
		<br/>
		<b><?= _l("Review"); ?></b>
		<textarea name="text" cols="40" rows="8" style="width: 98%;"></textarea>
		<span style="font-size: 11px;"><?= $text_note; ?></span><br/>
		<br/>
		<b><?= _l("Rating"); ?></b> <span><?= _l("Bad"); ?></span>&nbsp;
		<input type="radio" name="rating" value="1"/>
		&nbsp;
		<input type="radio" name="rating" value="2"/>
		&nbsp;
		<input type="radio" name="rating" value="3"/>
		&nbsp;
		<input type="radio" name="rating" value="4"/>
		&nbsp;
		<input type="radio" name="rating" value="5"/>
		&nbsp; <span><?= _l("Good"); ?></span><br/>
		<br/>
		<b><?= _l("Captcha"); ?></b><br/>
		<input type="text" name="captcha" value=""/>
		<br/>
		<img src="index.php?route=product/product/captcha" alt="" id="captcha"/><br/>
		<br/>

		<div class="buttons">
			<div class="right"><a id="button-review" class="button"><?= $button_continue; ?></a></div>
		</div>
	</div>
<? } ?>

<script type="text/javascript">
	$('#review .pagination a')
	click(function () {
		$('#review').slideUp('slow');

		$('#review').load(this.href);

		$('#review').slideDown('slow');

		return false;
	});

	$('#review').load("<?= HTTP_CATALOG . "index.php?route=product/product/review"; ?>" + '&product_id=<?= $product_id; ?>');

	$('#button-review').bind('click', function () {
		$.ajax({
			url: "<?= HTTP_CATALOG . "index.php?route=product/product/write"; ?>" + '&product_id=<?= $product_id; ?>',
			type: 'post',
			dataType: 'json',
			data: 'name=" + encodeURIComponent($("input[name=\'name\']').val()
		)
		+'&text=" + encodeURIComponent($("textarea[name=\'text\']'
		).
		val()
		)
		+'&rating=" + encodeURIComponent($("input[name=\'rating\']:checked'
		).
		val() ? $('input[name=\'rating\']:checked').val() : ''
		)
		+'&captcha=" + encodeURIComponent($("input[name=\'captcha\']'
		).
		val()
		),
		beforeSend: function () {
			$('.success, .warning').remove();
			$('#button-review').attr('disabled', true);
			$('#review-title').after('<div class="attention"><img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /> <?= $text_wait; ?></div>');
		}
		,
		complete: function () {
			$('#button-review').attr('disabled', false);
			$('.attention').remove();
		}
		,
		success: function (data) {
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
	})
	;
</script>
