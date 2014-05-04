<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
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
							<? $this->builder->setConfig('domain', 'domain'); ?>
							<?= $this->builder->build('select', $data_sites, 'domain', $domain); ?>
						</td>
					</tr>
					<tr>
						<td><?= _l("Sync Table"); ?></td>
						<td>
							<?= $this->builder->build('multiselect', $data_tables, 'tables', $tables); ?>
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

<?= _call('common/footer'); ?>
