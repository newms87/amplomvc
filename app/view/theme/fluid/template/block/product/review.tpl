<div class="review">
	<div>
		<img src="<?= URL_THEME_IMAGE . "stars-$rating.png"; ?>" alt="<?= $reviews; ?>"/>
		<a onclick="$('a[href=\'#tab-review\']').trigger('click');"><?= $reviews; ?></a>
		<a onclick="$('a[href=\'#tab-review\']').trigger('click');"><?= _l("Write a review"); ?></a>
	</div>
</div>
