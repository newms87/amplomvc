<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
							<td class="left"><?= $column_keyword; ?></td>
							<td class="left"><?= $column_route; ?></td>
							<td class="left"><?= $column_query; ?></td>
							<td class="left"><?= $column_redirect; ?></td>
							<td class="left"><?= $column_store; ?></td>
							<td class="left"><?= $column_status; ?></td>
							<td class="right"><?= $column_action; ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($aliases) { ?>
						<? foreach ($aliases as $alias) { ?>
						<tr>
							<td style="text-align: center;">
								<input type="checkbox" name="selected[]" value="<?= $alias['url_alias_id']; ?>" <?= $alias['selected']?"checked='checked'":""; ?> />
							</td>
							<td class="left"><?= $alias['keyword']; ?></td>
							<td class="left"><?= $alias['route']; ?></td>
							<td class="left"><?= $alias['query']; ?></td>
							<td class="left"><?= $alias['redirect']; ?></td>
							<td class="left"><?= $data_stores[$alias['store_id']]['name']; ?></td>
							<td class="left"><?= $alias['status']?'Enabled':'Disabled'; ?></td>
							<td class="right">
								[ <a href="<?= $alias['action']['href']; ?>"><?= $alias['action']['text']; ?></a> ]
							</td>
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