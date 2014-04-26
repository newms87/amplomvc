<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= _l("Return Policies"); ?></h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td valign="top"><a id="add_policy" class="button"><?= _l("Add Return Policy"); ?></a></td>
						<td>
							<ul id="return_policy_list" class="easy_list">
								<? foreach ($return_policies as $row => $policy) { ?>
									<li class="return_policy" data-row="<?= $row; ?>">
										<input class="title" size="50" type="text" name="return_policies[<?= $row; ?>][title]" value="<?= $policy['title']; ?>"/><br/>
										<textarea class="description ckedit" name="return_policies[<?= $row; ?>][description]"><?= $policy['description']; ?></textarea>

										<div class="return_days_box">
											<?= $this->builder->build('select', $data_days, 'data_days', $policy['days'] > 0 ? 1 : $policy['days']); ?>
											<input type="text" size="2" name="return_policies[<?= $row; ?>][days]" value="<?= $policy['days']; ?>"/>
										</div>

										<? if (empty($policy['no_delete'])) { ?>
											<a class="button delete text" onclick="$(this).closest('li').remove()"><?= _l("Delete"); ?></a>
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

<? foreach ($return_policies as $key => $policy) { ?>
	<?= $this->builder->js('translations', $policy['translations'], "return_policies[$key][%name%]"); ?>
<? } ?>

<script type="text/javascript">
	$('[name=data_days]').change(function () {
		days_input = $(this).closest('.return_days_box').find('input');
		if ($(this).val() == 'final') {
			days_input.hide().val(-1);
		}
		else if ($(this).val() == 0) {
			days_input.hide().val(0);
		}
		else {
			days_input.show();

			if (!parseInt(days_input.val()) || parseInt(days_input.val()) < 1) days_input.val(<?= $return_policies['__ac_template__']['days']; ?>);
		}
	}).change();

	$('#return_policy_list').ac_template('rp_list', {defaults: <?= json_encode($return_policies['__ac_template__']); ?>});
	$('#add_policy').click(function () {
		$.ac_template('rp_list', 'add')
	});

	$('#return_policy_list').sortable();
</script>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= $this->builder->js('ckeditor'); ?>

<?= _call('common/footer'); ?>
