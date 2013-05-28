=====
<div class="right">
	<div class="description">
-----
<<<<<
		<? if ($manufacturer) { ?>
-----
>>>>> {html}
		<? if($collection) { ?>
			<div class="description_manufacturer">
				<span><?= $text_collection; ?></span>
				<a href="<?= $collection['href']; ?>" class="manufacturer_link"><?= $collection['name']; ?></a>
			</div>
		<? } elseif ($manufacturer) { ?>
-----