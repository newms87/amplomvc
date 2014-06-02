<?= call('common/header'); ?>
<div class="section">
	<?= breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/backup.png'); ?>" alt=""/> <?= _l("Synchronize"); ?></h1>

			<div class="buttons">
				<a href="<?= $return; ?>" class="button"><?= _l("Return to Dev Console"); ?></a>
			</div>
		</div>
		<div class="section">
			<form id="request_sync_table" action="" method="post">
				<table class="form">
					<tr>
						<td>
							<label><?= _l("Synchronize Site"); ?></label>
							<?= build('select', array(
								'name'   => 'domain',
								'data'   => $data_sites,
								'select' => $domain,
								'key'    => 'domain',
								'value'  => 'domain',
							)); ?>
						</td>
					</tr>
					<tr>
						<td><?= _l("Sync Table"); ?></td>
						<td><?= build('multiselect', array(
								'name'   => 'tables',
								'data'   => $data_tables,
								'select' => $tables
							)); ?>
						</td>
					</tr>
					<tr>
						<td><label for="password"><?= _l("Password"); ?></label></td>
						<td><input id="password" type="password" name="password" value=""/></td>
					</tr>
					<tr>
						<td><input type="submit" class="button" name="sync_site" value="<?= _l("Sync"); ?>"/></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<?= call('common/footer'); ?>
