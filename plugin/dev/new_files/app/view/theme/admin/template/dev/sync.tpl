<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>
		</div>
		<div class="section">
			<form id="request_sync_table" action="" method="post">
				<table class="form">
					<tr>
						<td>
							<label>{{Synchronize Site}}</label>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'domain',
								'data'   => $data_sites,
								'select' => $domain,
								'value'  => 'domain',
								'label'  => 'domain',
							)); ?>
						</td>
					</tr>
					<tr>
						<td>{{Sync Table}}</td>
						<td><?= build(array(
								'type'   => 'multiselect',
								'name'   => 'tables',
								'data'   => $data_tables,
								'select' => $tables
							)); ?>
						</td>
					</tr>
					<tr>
						<td><label for="password">{{Password}}</label></td>
						<td><input id="password" type="password" name="password" value=""/></td>
					</tr>
					<tr>
						<td><input type="submit" class="button" name="sync_site" value="{{Sync}}"/></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
