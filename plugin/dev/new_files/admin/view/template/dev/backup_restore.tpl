<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs();?>
	<?= $this->builder->display_errors($errors);?>
	<div class="box">
		<div class="heading">
			<h1><img src="view/image/backup.png" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a href="<?=$return;?>" class="button"><?= $button_return; ?></a>
			</div>
		</div>
		<div class="content">
			<form id="site_backup_restore" action="" method="post">
				<table class="form">
					<tr>
						<td>
							<label><?= $entry_backup;?></label>
							<input type="submit" class="button" name="site_backup" value="<?= $button_backup;?>" />
						</td>
						<td>
							<?= $this->builder->build('multiselect', $data_tables, 'tables', $tables);?>
						</td>
					</tr>
					<tr>
						<td><?= $entry_restore; ?></td>
						<td>
							<? $this->builder->set_config('path', 'name');?>
							<?= $this->builder->build('radio', $data_backup_files, 'backup_file');?>
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
							<label><?= $entry_execute_file;?></label>
							<input type="submit" class="button" name="execute_file" value="<?= $button_execute_file;?>" />
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