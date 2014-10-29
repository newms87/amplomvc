<?= IS_AJAX ? '' : call('admin/header'); ?>
	<div class="section">
		<?= IS_AJAX ? '' : breadcrumbs(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= theme_url('image/backup.png'); ?>" alt=""/> <?= _l("Database Administration"); ?></h1>

				<div class="buttons">
					<a href="<?= $return; ?>" class="button"><?= _l("Return to Dev Console"); ?></a>
				</div>
			</div>
			<div class="section">
				<form id="form_db_query" action="" method="post">
					<table class="form">
						<tr>
							<td><label for="db_query"><?= _l("Backup"); ?></label></td>
							<td>
								<textarea name="query" id="db_query" rows="10" cols="100"><?= $query; ?></textarea>
							</td>
							<td id="db_tables">
								<?= build('multiselect', array(
									'name' => 'tables',
									'data' => $data_tables,
								)); ?>
							</td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" class="button" name="submit_query" value="<?= _l("Submit Query"); ?>"/>
							</td>
						</tr>
						<? if (!empty($results)) { ?>
							<tr>
								<td colspan="3">
									<? $key_list = array_keys(current($results)); ?>
									<table class="list query">
										<thead>
											<tr>
												<? foreach ($key_list as $key) { ?>
													<th><?= $key; ?></th>
												<? } ?>
											</tr>
										</thead>
										<tbody>
											<? foreach ($results as $row) { ?>
												<tr>
													<? foreach ($row as $key => $value) { ?>
														<td><?= $value; ?></td>
													<? } ?>
												</tr>
											<? } ?>
										</tbody>
										<tfoot>
											<tr>
												<? foreach ($key_list as $key) { ?>
													<td><?= $key; ?></td>
												<? } ?>
											</tr>
										</tfoot>
									</table>
								</td>
							</tr>
						<? } ?>
					</table>
				</form>
			</div>
		</div>
	</div>

<?= IS_AJAX ? '' : call('admin/footer'); ?>
