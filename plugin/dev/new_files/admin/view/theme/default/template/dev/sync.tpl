<?= $header; ?>
<div class="content">
	<?= $breadcrumbs; ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'backup.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a href="<?= $return; ?>" class="button"><?= $button_return; ?></a>
			</div>
		</div>
		<div class="content">
			<form id="request_sync_table" action="" method="post">
				<table class="form">
					<tr>
						<td>
							<label><?= $text_sync_site; ?></label>
							<? $this->builder->set_config('domain', 'domain');?>
							<?= $this->builder->build('select', $data_sites, 'domain', $domain); ?>
						</td>
					</tr>
					<tr>
						<td><?= $entry_sync_table; ?></td>
						<td>
							<?= $this->builder->build('multiselect', $data_tables, 'tables', $tables); ?>
						</td>
					</tr>
					<tr>
						<td><label for="password"><?= $entry_password; ?></label></td>
						<td><input id="password" type="password" name="password" value="" /></td>
					</tr>
					<tr>
						<td><input type="submit" class="button" name="sync_site" value="<?= $button_sync; ?>" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<?= $footer; ?>