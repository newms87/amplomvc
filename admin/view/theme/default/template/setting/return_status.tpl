<?= $this->call('common/header'); ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= _l("Return Statuses"); ?></h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td valign="top"><a id="add_status" class="button"><?= _l("Add Return Status"); ?></a></td>
						<td>
							<ul id="return_status_list" class="easy_list">
								<? foreach ($return_statuses as $row => $status) { ?>
									<li class="return_status" data-row="<?= $row; ?>">
										<input class="title" size="50" type="text" name="return_statuses[<?= $row; ?>][title]" value="<?= $status['title']; ?>"/><br/>
										<? if (empty($status['no_delete'])) { ?>
											<a class="delete button text" onclick="$(this).closest('li').remove()"><?= _l("Delete"); ?></a>
										<? } ?>
									</li>
								<? } ?>
							</ul>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<? foreach ($return_statuses as $key => $status) { ?>
	<?= $this->builder->js('translations', $status['translations'], "return_statuses[$key][%name%]"); ?>
<? } ?>

<script type="text/javascript">
	$('#return_status_list').ac_template('rs_list', {defaults: <?= json_encode($return_statuses['__ac_template__']); ?>});
	$('#add_status').click(function () {
		$.ac_template('rs_list', 'add')
	});

	$('#return_status_list').sortable();
</script>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= $this->call('common/footer'); ?>
