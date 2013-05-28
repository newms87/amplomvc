<div class="box">
	<div class="box-heading"><?= $heading_title; ?></div>
	<div class="box-content">
		<div class="box-category">
			<ul>
				<? foreach ($categories as $category) { ?>
				<li>
					<? if ($category['category_id'] == $category_id) { ?>
					<a href="<?= $category['href']; ?>" class="active"><?= $category['name']; ?></a>
					<? } else { ?>
					<a href="<?= $category['href']; ?>"><?= $category['name']; ?></a>
					<? } ?>
					<? if ($category['children']) { ?>
					<ul>
						<? foreach ($category['children'] as $child) { ?>
						<li>
							<? if ($child['category_id'] == $child_id) { ?>
							<a href="<?= $child['href']; ?>" class="active"> - <?= $child['name']; ?></a>
							<? } else { ?>
							<a href="<?= $child['href']; ?>"> - <?= $child['name']; ?></a>
							<? } ?>
						</li>
						<? } ?>
					</ul>
					<? } ?>
				</li>
				<? } ?>
			</ul>
		</div>
	</div>
</div>
