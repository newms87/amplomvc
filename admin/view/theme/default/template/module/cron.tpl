<?= $header; ?>
<div class="content">
	<?= $breadcrumbs; ?>
	<? if($errors){?>
		<div class="message_box warning">
		<? $br=false; foreach($errors as $e){ echo ($br?'<br>':'') . $e; $br=true;}?>
		</div>
	<? }?>
<div class="box">
	<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
		<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
	</div>
	<div class="content">
		<a href='<?= $run_cron; ?>' target="_blank" class="button run_cron_button"><?= $button_run_cron; ?></a>
		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
			<table id="module" class="list">
				<thead>
					<tr>
				<td class="left"><?= $entry_name; ?></td>
						<td class="left"><?= $entry_action; ?></td>
						<td class="left"><?= $entry_status; ?></td>
						<td class="right"><?= $entry_sort_order; ?></td>
						<td></td>
					</tr>
				</thead>
				<? foreach ($tasks as $row=>$task) { ?>
				<tbody id="module-row<?= $row; ?>">
					<tr>
					<td class="left"><input type="text" name="tasks[<?= $row; ?>][name]" value="<?= $tasks[$row]['name']; ?>" size="30" maxlength='30' /></td>
					<td class="left"><input type="text" name="tasks[<?= $row; ?>][action]" value="<?= $tasks[$row]['action']; ?>" size="100" maxlength='100' /></td>
						<td class="left"><?= $this->builder->build('select',$statuses, "tasks[$row][status]", (int)$tasks[$row]['status']); ?></td>
						<td class="left">
							<ul class='time_list'>
									<? if(isset($tasks[$row]['times'])) {?>
									<? foreach($tasks[$row]['times'] as $time) { ?>
									<li>
										<input type="text" name="tasks[<?= $row; ?>][times][]" value="<?= $time; ?>" class='time' />
										<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" onclick="$(this).parent().remove();" />
									</li>
									<? } ?>
									<? }?>
							</ul>
							<div style="text-align:center"><a onclick="add_cron_time($(this), <?= $row; ?>);"><?= $text_add_cron_time; ?></a></div>
						</td>
						<td class="right"><input type="text" name="tasks[<?= $row; ?>][sort_order]" value="<?= $tasks[$row]['sort_order']; ?>" size="3" /></td>
						<td class="left"><a onclick="$('#module-row<?= $row; ?>').remove();" class="button"><?= $button_remove; ?></a></td>
					</tr>
				</tbody>
				<? } ?>
				<tfoot>
					<tr>
						<td colspan="8"></td>
						<td class="left"><a onclick="addModule();" class="button"><?= $button_add_task; ?></a></td>
					</tr>
				</tfoot>
			</table>
		</form>
	</div>
</div>

<script type="text/javascript">//<!--
function add_cron_time(context, row){
	html =	'<li>';
	html +=		'<input type="text" name="tasks[%row%][times][]" value="" class="time" />';
	html +=		'<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" onclick="$(this).parent().remove();" />';
	html += '</li>';
	
	context.closest('td').find('.time_list').append(html.replace(/%row%/g, row))
	.children().last().find('.time').timepicker({timeFormat: 'h:m'});
}

var task_row = <?= count($tasks); ?>;

function addModule() {
	html	= '<tbody id="module-row%modrow%">';
	html += '	<tr>';
	html += '		<td class="left"><input type="text" name="tasks[%modrow%][name]" value="" size="30" maxlength="30" /></td>';
	html += '		<td class="left"><input type="text" name="tasks[%modrow%][action]" value="" size="100" maxlength="100" /></td>';
	html += '		<td class="left">' + "<?= $this->builder->build('select',$statuses,'tasks[%modrow%][status]',1); ?>" + '</td>';
	html += '		<td class="right"><input type="text" name="tasks[%modrow%][sort_order]" value="0" size="3" /></td>';
	html += '		<td class="left"><a onclick="$(\'#module-row%modrow%\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html += '	</tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html.replace(/%modrow%/g,task_row));
	task_row++;
}
//--></script>

<?= $this->builder->js('datepicker', true); ?>

<?= $footer; ?>