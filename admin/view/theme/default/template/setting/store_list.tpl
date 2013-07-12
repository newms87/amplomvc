<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
		</div>
		<div class="content">
			<div class="menu_icons">
				<a class="menu_item" href="<?= $admin_settings; ?>">
					<div class="title"><?= $button_admin_settings; ?></div>
					<div class="image"><img src="<?= HTTP_THEME_IMAGE . "admin_settings.png"; ?>" /></div>
				</a>
				<a class="menu_item" href="<?= $system_update; ?>">
					<div class="title"><?= $button_system_update; ?></div>
					<div class="image"><img src="<?= HTTP_THEME_IMAGE . "system_update.png"; ?>" /></div>
				</a>
			</div>
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
							<td class="left"><?= $column_name; ?></a></td>
							<td class="left"><?= $column_url; ?></td>
							<td class="right"><?= $column_action; ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($data_stores) { ?>
						<? foreach ($data_stores as $store) { ?>
						<tr>
							<td style="text-align: center;">
								<input type="checkbox" name="selected[]" value="<?= $store['store_id']; ?>" <?= $store['selected'] ? 'checked':''; ?>/>
							</td>
							<td class="left"><?= $store['name']; ?></td>
							<td class="left"><?= $store['url']; ?></td>
							<td class="right"><? foreach ($store['action'] as $action) { ?>
								[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
								<? } ?></td>
						</tr>
						<? } ?>
						<? } else { ?>
						<tr>
							<td class="center" colspan="4"><?= $text_no_results; ?></td>
						</tr>
						<? } ?>
					</tbody>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?> 