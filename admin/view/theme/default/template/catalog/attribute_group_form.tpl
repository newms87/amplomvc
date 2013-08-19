<?= $header; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'order.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
					<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
				</div>
			</div>
			<div class="content">
				<form action="<?= $save; ?>" method="post" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_name; ?></td>
							<td><input type="text" name="name" value="<?= $name; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_sort_order; ?></td>
							<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1"/></td>
						</tr>
						<tr>
							<td><?= $entry_attributes; ?></td>
							<td>
								<table class="list">
									<thead>
									<tr>
										<td class="center"><?= $entry_attribute_name; ?></td>
										<td class="center"><?= $entry_attribute_sort_order; ?></td>
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
												<input type="text" class="sort_order" name="attributes[<?= $row; ?>][sort_order]" value="<?= $attribute['sort_order']; ?>"/>
											</td>
											<td class="center">
												<? if (!empty($attribute['product_count'])) { ?>
													<span class="product_count"><?= $attribute['product_count']; ?></span>
												<? } else { ?>
													<a class="button" onclick="$(this).closest('.attribute').remove()"><?= $button_remove; ?></a>
												<? } ?>
											</td>
										</tr>
									<? } ?>

									</tbody>
									<tfoot>
									<tr>
										<td><a id="add_attribute" class="button"><?= $button_add_attribute; ?></a></td>
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

<script type="text/javascript">//<!--
var a_list = $('#attribute_list');
a_list.ac_template('a_list');

$('#add_attribute').click(function () {
	$.ac_template('a_list', 'add');
	a_list.update_index('.sort_order');
});

a_list.sortable({cursor: 'move', stop: function () {
	$(this).update_index('.sort_order');
}});
//--></script>

<?= $this->builder->js('errors', $errors); ?>

<? foreach ($attributes as $attribute) { ?>
	<?= $this->builder->js('translations', $attribute['translations'], "attributes[$attribute[attribute_id]][%name%]"); ?>
<? } ?>

<?= $footer; ?>