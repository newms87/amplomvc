<?= $header; ?>
<div class="section">
<?= $this->breadcrumb->render(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt=""/> <?= _("Cron Jobs"); ?></h1>

			<div class="buttons">
				<a href="<?= $run_cron; ?>" target="_blank" class="button run_cron_button"><?= _("Run Cron"); ?></a>
				<a onclick="$('#form').submit();" class="button"><?= _("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<table id="module" class="list">
					<thead>
						<tr>
							<td class="left"><?= _("Name"); ?></td>
							<td class="left"><?= _("Action"); ?><span class="help"><?= _("These files are located in " . DIR_CRON); ?></span></td>
							<td class="left"><?= _("Time"); ?></td>
							<td class="right"><?= _("Sort Order"); ?></td>
							<td class="left"><?= _("Status"); ?></td>
							<td></td>
						</tr>
					</thead>

					<tbody id="task_list">
					<? foreach ($tasks as $row => $task) { ?>
						<tr class="task" data-row="<?= $row; ?>">
							<td class="left"><input type="text" name="tasks[<?= $row; ?>][name]" value="<?= $tasks[$row]['name']; ?>" size="30" maxlength="30"/></td>
							<td class="left">
								<label for="select_file<?= $row; ?>"><?= _("Cron File"); ?></label>
								<select id="select_file<?= $row; ?>" name="tasks[<?= $row; ?>][file]">
								<? foreach ($data_files as $key => $file) { ?>
									<option value="<?= $file; ?>" data-key="<?= $key; ?>"><?= $file; ?></option>
								<? } ?>
								</select>

								<? foreach ($data_methods as $key => $methods) { ?>
									<select data-key="<?= $key; ?>" name="tasks[<?= $row; ?>][method]">
										<? foreach ($methods as $method) { ?>
											<option value="<?= $method; ?>"><?= $method; ?></option>
										<? } ?>
									</select>
								<? } ?>
							</td>
							<td class="left">
								<table class="crontime">
									<thead>
										<tr>
											<td><?= _("Minute"); ?></td>
											<td><?= _("Hour"); ?></td>
											<td><?= _("Day of Month"); ?></td>
											<td><?= _("Month"); ?></td>
											<td><?= _("Day of Week"); ?></td>
										</tr>
									</thead>
									<tr>
										<td><input type="text" size="3" name="tasks[<?= $row; ?>][time][i]" value="<?= $tasks[$row]['time']['i']; ?>"/></td>
										<td><input type="text" size="3" name="tasks[<?= $row; ?>][time][h]" value="<?= $tasks[$row]['time']['h']; ?>"/></td>
										<td><input type="text" size="3" name="tasks[<?= $row; ?>][time][d]" value="<?= $tasks[$row]['time']['d']; ?>"/></td>
										<td><input type="text" size="3" name="tasks[<?= $row; ?>][time][m]" value="<?= $tasks[$row]['time']['m']; ?>"/></td>
										<td><input type="text" size="3" name="tasks[<?= $row; ?>][time][w]" value="<?= $tasks[$row]['time']['w']; ?>"/></td>
									</tr>
								</table>
							</td>
							<td class="right"><input type="text" class="sort_order" name="tasks[<?= $row; ?>][sort_order]" value="<?= $tasks[$row]['sort_order']; ?>" size="3"/></td>
							<td class="left"><?= $this->builder->build('select', $data_statuses, "tasks[$row][status]", $tasks[$row]['status']); ?></td>
							<td class="left"><a onclick="$('#module-row<?= $row; ?>').remove();" class="button_remove"></a></td>
						</tr>
					<? } ?>
					</tbody>

					<tfoot>
						<tr>
							<td colspan="6" class="center"><a id="add_task" class="button"><?= _("Add Task"); ?></a></td>
						</tr>
					</tfoot>
				</table>
			</form>
		</div>
	</div>

<script type="text/javascript">//<!--
	$('#task_list').ac_template('task_list', {defaults: <?= json_encode($tasks['__ac_template__']); ?>});

	$('#add_task').click(function(){
		$.ac_template('task_list', 'add');
		$('#task_list').update_index();
	});

	$('#task_list').sortable({stop: function(){
		$(this).update_index();
	}});
//--></script>

<?= $this->builder->js('datepicker', true); ?>

<?= $footer; ?>
