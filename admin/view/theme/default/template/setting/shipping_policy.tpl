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
							<ul id="shipping_policy_list" class="easy_list">
							
								<? $max_row = 0; ?>
								<? foreach ($shipping_policies as $key => $policy) { ?>
									<li class="shipping_policy <?= $key; ?>">
										<input class="title" size="50" type="text" name="shipping_policies[<?= $key; ?>][title]" value="<?= $policy['title']; ?>" /><br />
										<textarea class="description ckedit" name="shipping_policies[<?= $key; ?>][description]"><?= $policy['description']; ?></textarea>
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

<? foreach ($shipping_policies as $key => $policy) { ?>
	<?= $this->builder->js('translations', $policy['translations'], "shipping_policies[$key][%name%]"); ?>
<? } ?>

<?= $this->builder->js('errors',$errors); ?>

<script type="text/javascript">//<!--
$('#shipping_policy_list').sortable();
//--></script>

<?= $this->builder->js('template_rows', '#shipping_policy_list', '#add_status', $max_row+1, $template_row_defaults); ?>

<?= $this->builder->js('ckeditor'); ?>

<?= $footer; ?>