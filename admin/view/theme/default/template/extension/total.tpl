<?= _call('common/header'); ?>

<div class="section">
	<?= _breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/module.png'); ?>" alt=""/> <?= $page_title; ?></h1>

			<div class="buttons">
				<a onclick="$('#form').trigger('saving').submit();" class="button"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="extension_settings">
					<table class="form">
						<tr>
							<td><?= _l("Total Label"); ?></td>
							<td><input type="text" name="title" value="<?= $title; ?>"/></td>
						</tr>
						<? if (!empty($extend_settings)) { ?>
							<tr>
								<td colspan="2"><?= $extend_settings; ?></td>
							</tr>
						<? } ?>
						<tr>
							<td><?= _l("Sort Order"); ?></td>
							<td><input type="text" name="sort_order" value="<?= $sort_order; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Status"); ?></td>
							<td><?= $this->builder->build('select', $data_statuses, "status", $status); ?></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= _call('common/footer'); ?>
