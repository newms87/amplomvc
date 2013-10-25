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
							<td class="center"><?= _("Last Run"); ?></td>
							<td class="left"><?= _("Status"); ?></td>
							<td></td>
						</tr>
					</thead>

					<tbody id="task_list">
					<? foreach ($tasks as $row => $task) { ?>
						<tr class="task" data-row="<?= $row; ?>">
							<td class="left"><input type="text" name="tasks[<?= $row; ?>][name]" value="<?= $task['name']; ?>" size="30" maxlength="30"/></td>
							<td class="left cron_action">

								<label for="select_file<?= $row; ?>"><?= _("Cron File"); ?></label>
								<select id="select_file<?= $row; ?>" class="select_file" name="tasks[<?= $row; ?>][file]">
								<? foreach ($data_files as $file) { ?>
									<option value="<?= $file; ?>" <?= $file === $task['file'] ? 'selected="selected"' : ''; ?>><?= $file; ?></option>
								<? } ?>
								</select>

								<br/><br/>
								<label><?= _("Method"); ?></label>
								<span class="method_list">
									<? foreach ($data_methods as $file => $methods) { ?>
										<select class="select_method" data-file="<?= $file; ?>" data-name="tasks[<?= $row; ?>][method]">
											<? foreach ($methods as $method) { ?>
												<option value="<?= $method; ?>" <?= $method === $task['method'] ? 'selected="selected"' : ''; ?>><?= $method; ?></option>
											<? } ?>
										</select>
									<? } ?>
								</span>
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
										<td><input type="text" size="3" name="tasks[<?= $row; ?>][time][i]" value="<?= $task['time']['i']; ?>"/></td>
										<td><input type="text" size="3" name="tasks[<?= $row; ?>][time][h]" value="<?= $task['time']['h']; ?>"/></td>
										<td><input type="text" size="3" name="tasks[<?= $row; ?>][time][d]" value="<?= $task['time']['d']; ?>"/></td>
										<td><input type="text" size="3" name="tasks[<?= $row; ?>][time][m]" value="<?= $task['time']['m']; ?>"/></td>
										<td><input type="text" size="3" name="tasks[<?= $row; ?>][time][w]" value="<?= $task['time']['w']; ?>"/></td>
									</tr>
								</table>
							</td>
							<td class="right"><input type="text" class="sort_order" name="tasks[<?= $row; ?>][sort_order]" value="<?= $task['sort_order']; ?>" size="3"/></td>
							<td class="center">
								<?= empty($task['last_run']) ? _("Never") : $task['last_run']; ?>
								<input type="hidden" name="tasks[<?= $row; ?>][last_run]" value="<?= $task['last_run']; ?>" />
							</td>
							<td class="left"><?= $this->builder->build('select', $data_statuses, "tasks[$row][status]", $task['status']); ?></td>
							<td class="left"><a onclick="$(this).closest('.task').remove();" class="button_remove"></a></td>
						</tr>
					<? } ?>
					</tbody>

					<tfoot>
						<tr>
							<td colspan="7" class="center"><a id="add_task" class="button"><?= _("Add Task"); ?></a></td>
						</tr>
					</tfoot>
				</table>
			</form>
		</div>
	</div>

<script type="text/javascript">//<!--
	$('#task_list .select_file').change(function(){
		var list = $(this).siblings('.method_list');
		list.find('.select_method').hide().removeAttr('name');

		var method = list.find('.select_method[data-file="' + $(this).val() +'"]').show();
		method.attr('name', method.attr('data-name'));
	}).change();

	$('#task_list').ac_template('task_list', {defaults: <?= json_encode($tasks['__ac_template__']); ?>});

	$('#add_task').click(function(){
		$.ac_template('task_list', 'add');
		$('#task_list').update_index();
	});

	$('#task_list').sortable({stop: function(){
		$(this).update_index();
	}});
//--></script>

<?= $footer; ?>
