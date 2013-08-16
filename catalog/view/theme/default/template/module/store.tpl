<div class="box">
	<div class="box_heading"><?= $head_title; ?></div>
	<div class="box_content" style="text-align: center;">
		<p><?= $text_store; ?></p>
		<select name="store" onchange="location = this.value">
			<? foreach ($stores as $store) { ?>
				<? if ($store['store_id'] == $store_id) { ?>
					<option value="<?= $store['url']; ?>" selected="selected"><?= $store['name']; ?></option>
				<? } else { ?>
					<option value="<?= $store['url']; ?>"><?= $store['name']; ?></option>
				<? } ?>
			<? } ?>
		</select>
		<br/>
		<br/>
	</div>
</div>
