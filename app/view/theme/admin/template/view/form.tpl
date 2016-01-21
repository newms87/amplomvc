<?= $is_ajax ? '' : call('admin/header'); ?>

<section class="section">
	<form id="client-form" action="<?= $save; ?>" method="post" class="box ctrl-save <?= $is_ajax ? 'ajax-form' : ''; ?>">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<button>{{Save}}</button>
			</div>
		</div>

		<div class="content">
			<table class="form">
				<tr>
					<td class="required">{{Name}}</td>
					<td><input type="text" name="name" value="<?= $name; ?>"/></td>
				</tr>
				<tr>
					<td>{{Slug}}</td>
					<td>
						<? if ($view_listing_id) { ?>
							<span><?= $slug; ?></span>
						<? } else { ?>
							<input type="text" name="slug" value="<?= $slug; ?>"/>
						<? } ?>
					</td>
				</tr>
				<tr>
					<td class="required">{{Path}}</td>
					<td><input type="text" name="path" value="<?= $path; ?>"/></td>
				</tr>
				<tr>
					<td>{{Query}}</td>
					<td><input type="text" name="query" value="<?= $query; ?>"/></td>
				</tr>
				<tr>
					<td>
						{{Create View SQL}}
						<span class="help">{{Must be a SELECT statement to create a view}}</span>
					</td>
					<td><textarea rows="8" cols="150" name="sql"><?= $sql; ?></textarea></td>
				</tr>
			</table>
		</div>
	</form>
</section>




<?= $is_ajax ? '' : call('admin/footer'); ?>
