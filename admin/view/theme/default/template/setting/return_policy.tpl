<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td valign="top"><a id="add_status" class="button"><?= $button_add; ?></a></td>
						<td>
							<ul id="return_policy_list" class="easy_list">
							
								<? $max_row = 0; ?>
								<? foreach ($return_policies as $key => $policy) { ?>
									<li class="return_policy <?= $key; ?>">
										<input class="title" size="50" type="text" name="return_policies[<?= $key; ?>][title]" value="<?= $policy['title']; ?>" /><br />
										<textarea class="description ckedit" name="return_policies[<?= $key; ?>][description]"><?= $policy['description']; ?></textarea>
										<div class="return_days_box">
											<?= $this->builder->build('select', $data_days, 'data_days', $policy['days'] > 0 ? 1 : $policy['days']); ?>
											<input type="text" size="2" name="return_policies[<?= $key; ?>][days]" value="<?= $policy['days']; ?>" />
										</div>
										 
										<? if (empty($policy['no_delete'])) { ?>
											<a class="delete_button text" onclick="$(this).closest('li').remove()"><?= $button_delete; ?></a>
										<? } ?>
									</li>
									<? if (is_integer($key)) { $max_row = max($max_row, $key); } ?>
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

<?= $this->builder->js('errors',$errors); ?>

<script type="text/javascript">//<!--
$('[name=data_days]').change(function(){
	days_input = $(this).closest('.return_days_box').find('input');
	if ($(this).val() == 'final') {
		days_input.hide().val(-1);
	}
	else if ($(this).val() == 0) {
		days_input.hide().val(0);
	}
	else {
		days_input.show().val(<?= $default_days; ?>);
	}
}).change();

$('#return_policy_list').sortable();
//--></script>

<?= $this->builder->js('template_rows', '#return_policy_list', '#add_status', $max_row+1, $template_row_defaults); ?>

<?= $this->builder->js('ckeditor'); ?>

<?= $footer; ?>