<?= $header; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'backup.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a href="<?= $return; ?>" class="button"><?= $button_return; ?></a>
				</div>
			</div>
			<div class="content">
				<form id="form_db_query" action="" method="post">
					<table class="form">
						<tr>
							<td><label for="db_query"><?= $entry_backup; ?></label></td>
							<td>
								<textarea name="query" id="db_query" rows="10" cols="100"><?= $query; ?></textarea>
							</td>
							<td id="db_tables">
								<?= $this->builder->build('multiselect', $data_tables, 'tables'); ?>
							</td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" class="button" name="submit_query" value="<?= $button_submit_query; ?>"/>
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

<?= $footer; ?>