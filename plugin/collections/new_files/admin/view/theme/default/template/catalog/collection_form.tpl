<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'user.png'; ?>" alt="" /> <?= $head_title; ?></h1>
			<div class="buttons">
				<a onclick="$('#form').submit()" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_name; ?></td>
						<td><input type="text" name="name" size="60" value="<?= $name; ?>" /></td>
					</tr>
					<tr>
						<td class="required"> <?= $entry_keyword; ?></td>
						<td><input type="text" name="keyword" size="60" value="<?= $keyword; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_image; ?></td>
						<?= $this->builder->set_builder_template('click_image'); ?>
						<td><?= $this->builder->image_input("image", $image, $thumb); ?></td>
					</tr>
					<tr>
						<td><?= $entry_meta_keywords; ?></td>
						<td><textarea name="meta_keywords" rows="4" cols="60"><?= $meta_keywords; ?></textarea></td>
					</tr>
					<tr>
						<td><?= $entry_meta_description; ?></td>
						<td><textarea name="meta_description" rows="8" cols="60"><?= $meta_description; ?></textarea></td>
					</tr>
					<tr>
						<td><?= $entry_description; ?></td>
						<td><textarea name="description" class="ckedit"><?= $description; ?></textarea></td>
					</tr>
					<tr>
						<td>
							<div><?= $entry_product; ?></div>
							<div><input type="text" id='product_list_autocomplete' /></div>
							<div><span class="help">(<?= $text_autocomplete; ?>)</span></div>
						</td>
						<td>
							<ol id="product_list" class="scrollbox editable_list">
								<? foreach ($products as $row => $product) { ?>
								<li data-row="<?= $row; ?>" data-id="<?= $product['product_id']; ?>">
									<div class='product editable_label'>
										<input type="hidden" class='ac_item_id' name="products[<?= $row; ?>][product_id]" value="<?= $product['product_id']; ?>" />
										<input type="text" size="60" name="products[<?= $row; ?>][name]" value="<?= $product['name']; ?>" />
									</div>
									<img onclick="$(this).parent().remove()" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />
								</li>
								<? } ?>
							</ol>
						</td>
					</tr>
					<tr>
						<td class="required"><?= $entry_category; ?></td>
						<? $this->builder->set_config('category_id', 'name');?>
						<td><?= $this->builder->build('multiselect', $data_categories, "categories", $categories); ?></td>
					</tr>
					<tr>
						<td class="required"><?= $entry_store; ?></td>
						<? $this->builder->set_config('store_id', 'name');?>
						<td><?= $this->builder->build('multiselect', $data_stores, "stores", $stores); ?></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><?= $this->builder->build('select',$data_statuses,'status',(int)$status); ?></td>
					</tr>
					<tr>
						<td><?= $entry_sort_order; ?></td>
						<td><input type="text" name="sort_order" size="1" value="<?= $sort_order; ?>" /></td>
					</tr>
				</table>
			</div>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('ckeditor'); ?>
<?= $this->builder->js('translations', $translations); ?>

<script type="text/javascript">//<!--
$('#product_list').ac_template('product_list', {unique: 'product_id'});

$('#product_list_autocomplete').autocomplete({
	delay: 0,
	source: function(request, response) {
		filter = {name: request.term};
		$.get("<?= $url_product_autocomplete; ?>", {filter: filter}, response, 'json');
	},
	select: autocomplete_callback_product
});

function autocomplete_callback_product(event, data) {
	$.ac_template('product_list', 'add', data.item);
	$(this).val('');
	return false;
}

$('#product_list').sortable({revert:true});
//--></script>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>