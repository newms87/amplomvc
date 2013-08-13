<div class="box">
	<div class="box_heading"><?= $head_title; ?></div>
	<div class="box_content">
		<ul>
			<? foreach ($informations as $information) { ?>
			<li><a href="<?= $information['href']; ?>"><?= $information['title']; ?></a></li>
			<? } ?>
			<li><a href="<?= $contact; ?>"><?= $text_contact; ?></a></li>
			<li><a href="<?= $sitemap; ?>"><?= $text_sitemap; ?></a></li>
		</ul>
	</div>
</div>
