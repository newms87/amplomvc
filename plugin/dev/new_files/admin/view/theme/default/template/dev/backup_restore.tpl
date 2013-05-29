<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'backup.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a href="<?= $return; ?>" class="button"><?= $button_return; ?></a>
			</div>
		</div>
		<div class="content">
			<form id="site_backup_restore" action="" method="post">
				<table class="form">
					<tr>
						<td>
							<label><?= $entry_backup; ?></label>
							<input type="submit" class="button" name="site_backup" value="<?= $button_backup; ?>" />
						</td>
						<td>
							<?= $this->builder->build('multiselect', $data_tables, 'tables', $tables); ?>
						</td>
					</tr>
					<tr>
						<td><?= $entry_restore; ?></td>
						<td>
							<? foreach($data_backup_files as $file){ ?>
								<span class="radio_button">
									<input type="radio" name="backup_file" value="<?= $file['path']; ?>" id="radio_button_<?= md5($file['path']); ?>" />
									<label for="radio_button_<?= md5($file['path']); ?>">
										<span class="date"><?= $file['display_date']; ?></span> -
										<span class="name"><?= $file['name']; ?></span> -
										<span class="size"><?= $file['display_size']; ?></span>
									</label>
								</span>
							<? } ?>
						</td>
					</tr>
					<tr>
						<td></td>
						<td><input type="submit" class="button" name="site_restore" value="<?= $button_restore; ?>" /></td>
					</tr>
				</table>
			</form>
			<form action="" method="post" enctype="multipart/form-data">
				<table class="form">
					<tr>
						<td>
							<label><?= $entry_execute_file; ?></label>
							<input type="submit" class="button" name="execute_file" value="<?= $button_execute_file; ?>" />
						</td>
						<td>
							<input type="file" name="filename" value="" />
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<?= $footer; ?>