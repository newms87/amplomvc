<?= !IS_AJAX ? call('admin/header') : ''; ?>

<section class="section">
	<?= !IS_AJAX ? breadcrumbs() : ''; ?>

	<form id="client-form" action="<?= $save; ?>" method="post" class="box ctrl-save <?= IS_AJAX ? 'ajax-form' : ''; ?>">
		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("%s View", $name); ?>
			</h1>

			<div class="buttons">
				<button data-loading="<?= _l("Saving..."); ?>"><?= _l("Save"); ?></button>
				<? if (!IS_AJAX) { ?>
				<a href="<?= site_url('admin/view'); ?>" class="button"><?= _l("Cancel"); ?></a>
				<? } ?>
			</div>
		</div>

		<div class="content">
			<table class="form">
				<tr>
					<td class="required"><?= _l("Name"); ?></td>
					<td><input type="text" name="name" value="<?= $name; ?>" /></td>
				</tr>
				<tr>
					<td><?= _l("Slug"); ?></td>
					<td>
					<? if ($view_listing_id) { ?>
						<span><?= $slug; ?></span>
					<? } else { ?>
						<input type="text" name="slug" value="<?= $slug; ?>" />
					<? } ?>
					</td>
				</tr>
				<tr>
					<td class="required"><?= _l("Path"); ?></td>
					<td><input type="text" name="path" value="<?= $path; ?>" /></td>
				</tr>
				<tr>
					<td><?= _l("Query"); ?></td>
					<td><input type="text" name="query" value="<?= $query; ?>" /></td>
				</tr>
				<tr>
					<td>
						<?= _l("Create View SQL"); ?>
						<span class="help"><?= _l("Must be a SELECT statement to create a view"); ?></span>
					</td>
					<td><textarea rows="8" cols="150" name="sql"><?= $sql; ?></textarea></td>
				</tr>
			</table>
		</div>
	</form>
</section>


<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= !IS_AJAX ? call('admin/footer') : ''; ?>
