<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= _l("Shipping Policies"); ?></h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td valign="top"><a id="add_policy" class="button"><?= _l("Add Shipping Policy"); ?></a></td>
						<td>
							<ul id="shipping_policy_list" class="easy_list">
								<? foreach ($shipping_policies as $row => $policy) { ?>
									<li class="shipping_policy" data-row="<?= $row; ?>">
										<input class="title" size="50" type="text" name="shipping_policies[<?= $row; ?>][title]" value="<?= $policy['title']; ?>"/><br/>
										<textarea class="description ckedit" name="shipping_policies[<?= $row; ?>][description]"><?= $policy['description']; ?></textarea>
										<? if (empty($policy['no_delete'])) { ?>
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

<? foreach ($shipping_policies as $key => $policy) { ?>
	<?= $this->builder->js('translations', $policy['translations'], "shipping_policies[$key][%name%]"); ?>
<? } ?>

<script type="text/javascript">
	$('#shipping_policy_list').ac_template('sp_list', {defaults: <?= json_encode($shipping_policies['__ac_template__']); ?>});
	$('#add_policy').click(function () {
		$.ac_template('sp_list', 'add')
	});

	$('#shipping_policy_list').sortable();
</script>

<?= $this->builder->js('ckeditor'); ?>
<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
