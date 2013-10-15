<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>

		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'layout.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
					<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
				</div>
			</div>
			<div class="section">
				<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_name; ?></td>
							<td><input type="text" name="name" value="<?= $name; ?>"/></td>
						</tr>
					</table>
					<table id="route" class="list">
						<thead>
						<tr>
							<td class="left"><?= $entry_store; ?></td>
							<td class="left"><?= $entry_route; ?></td>
							<td></td>
						</tr>
						</thead>

						<tbody id="route_list">
						<? foreach ($routes as $row => $route) { ?>
							<tr class="route" data-row="<?= $row; ?>">
								<td class="left">
									<? $this->builder->setConfig('store_id', 'name'); ?>
									<?= $this->builder->build('select', $data_stores, "routes[$row][store_id]", $route['store_id']); ?>
								</td>
								<td class="left"><input type="text" name="routes[<?= $row; ?>][route]" value="<?= $route['route']; ?>"/></td>
								<td class="left"><a onclick="$(this).closest('.route').remove();"
								                    class="delete_button"><?= $button_remove; ?></a></td>
							</tr>
						<? } ?>
						</tbody>
						<tfoot>
						<tr>
							<td colspan="2"></td>
							<td class="left"><a id="add_route" class="button"><?= $button_add_route; ?></a></td>
						</tr>
						</tfoot>
					</table>
				</form>
			</div>
		</div>
	</div>

<?= $this->builder->js('ckeditor'); ?>

	<script type="text/javascript">//<!--
		$('#route_list').ac_template('route_list');

		$('#add_route').click(function () {
			$.ac_template('route_list', 'add');
		});
//--></script>
<?= $footer; ?>
