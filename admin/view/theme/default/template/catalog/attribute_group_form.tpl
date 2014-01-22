<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'order.png'; ?>" alt=""/> <?= _l("Attribute Groups"); ?></h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>

		<div class="section">
			<form action="<?= $save; ?>" method="post" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Attribute Group Name:"); ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>"/></td>
					</tr>
					<tr>
						<td><?= _l("Sort Order:"); ?></td>
						<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1"/></td>
					</tr>
					<tr>
						<td><?= _l("Attributes:"); ?></td>
						<td>
							<table class="list">
								<thead>
									<tr>
										<td class="center"><?= _l("Attribute Name"); ?></td>
										<td class="center"><?= _l("Sort Order"); ?></td>
										<td></td>
									</tr>
								</thead>
								<tbody id="attribute_list">
									<? foreach ($attributes as $row => $attribute) { ?>
										<tr class="attribute" data-row="<?= $row; ?>">
											<td class="center">
												<input type="hidden" name="attributes[<?= $row; ?>][attribute_id]" value="<?= $row; ?>"/>
												<input type="text" name="attributes[<?= $row; ?>][name]" value="<?= $attribute['name']; ?>"/>
											</td>
											<td class="center">
												<div class="image">
													<?= $this->builder->imageInput("attributes[$row][image]", $attribute['image']); ?>
												</div>
											</td>
											<td class="center">
												<input type="text" class="sort_order" name="attributes[<?= $row; ?>][sort_order]" value="<?= $attribute['sort_order']; ?>"/>
											</td>
											<td class="center">
												<? if (!empty($attribute['product_count'])) { ?>
													<span class="product_count"><?= $attribute['product_count']; ?></span>
												<? } else { ?>
													<a class="button" onclick="$(this).closest('.attribute').remove()"><?= _l("Remove"); ?></a>
												<? } ?>
											</td>
										</tr>
									<? } ?>

								</tbody>
								<tfoot>
									<tr>
										<td><a id="add_attribute" class="button"><?= _l("Add Attribute"); ?></a></td>
									</tr>
								</tfoot>
							</table>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('translations', $translations); ?>

<script type="text/javascript">
	var a_list = $('#attribute_list');
	a_list.ac_template('a_list');

	$('#add_attribute').click(function () {
		$.ac_template('a_list', 'add');
		a_list.update_index('.sort_order');
	});

	a_list.sortable({cursor: 'move', stop: function () {
		$(this).update_index('.sort_order');
	}});
</script>

<?= $this->builder->js('errors', $errors); ?>

<? foreach ($attributes as $attribute) { ?>
	<?= $this->builder->js('translations', $attribute['translations'], "attributes[$attribute[attribute_id]][%name%]"); ?>
<? } ?>

<?= $footer; ?>
