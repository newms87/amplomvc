<?= IS_AJAX ? '' : call('admin/header'); ?>
<div class="section">
	<?= IS_AJAX ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("Controller Override"); ?></h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td valign="top"><a id="add_override" class="button"><?= _l("Add Controller Override"); ?></a></td>
						<td>
							<ul id="controller_override_list" class="easy_list">
								<? foreach ($controller_overrides as $row => $override) { ?>
									<li class="controller_override" data-row="<?= $row; ?>">
										<input class="original" size="50" type="text" name="controller_overrides[<?= $row; ?>][original]" value="<?= $override['original']; ?>"/>
										<input class="alternate" size="50" type="text" name="controller_overrides[<?= $row; ?>][alternate]" value="<?= $override['alternate']; ?>"/>
										<input class="condition" size="50" type="text" name="controller_overrides[<?= $row; ?>][condition]" value="<?= $override['condition']; ?>"/>
										<a class="delete button text" onclick="$(this).closest('li').remove()"><?= _l("Delete"); ?></a>
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

<script type="text/javascript">
	$('#controller_override_list').ac_template('co_list', {defaults: <?= json_encode($controller_overrides['__ac_template__']); ?>});
	$('#add_override').click(function () {
		$.ac_template('co_list', 'add')
	});
</script>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= IS_AJAX ? '' : call('admin/footer'); ?>
