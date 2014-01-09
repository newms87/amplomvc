<? if (count($languages) > 1) { ?>
	<div id="language_menu">
		<div class="title"><?= _l("Language"); ?></div>
		<div class="language_list">
			<? foreach ($languages as $language) { ?>
				<a class="language_item" href="<?= $action . $language['code']; ?>">
					<img src="<?= $language['thumb']; ?>" alt="<?= $language['name']; ?>" title="<?= $language['name']; ?>"/>
				</a>
			<? } ?>
		</div>
	</div>
<? } ?>
