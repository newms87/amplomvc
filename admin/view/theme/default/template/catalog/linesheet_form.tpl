<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs(); ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'user.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a onclick="$('#form').submit()" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> <?= $entry_name; ?></td>
						<td><input type="text" name="name" size="60" value="<?= $name; ?>" /></td>
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
						<div><input type="text" id='product_list_autocomplete' filter="filter_name" route="catalog/product/autocomplete&filter_status=1" /></div>
						<div><?= $text_autocomplete; ?></div>
					</td>
					<td>
						<ol id="product_list" class="scrollbox editable_list">
							<? if(!empty($products)) { ?>
							<? foreach ($products as $product) { ?>
							<li>
								<div class='editable_label'>
										<input type="hidden" class='ac_item_id' name="products[<?= $product['product_id']; ?>][product_id]" value="<?= $product['product_id']; ?>" />
										<input type="text" size="60" name="products[<?= $product['product_id']; ?>][name]" value="<?= $product['name']; ?>" />
								</div>
								<img onclick="$(this).parent().remove()" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />
							</li>
							<? } ?>
							<? } ?>
						</ol>
					</td>
				</tr>
					<tr>
						<td class="required"> <?= $entry_store; ?></td>
						<? $this->builder->set_config('store_id', 'name');?>
						<td><?= $this->builder->build('multiselect', $data_stores, "stores", $stores); ?></td>
					</tr>
					<tr>
						<td><?= $entry_status; ?></td>
						<td><?= $this->builder->build('select',$data_statuses,'status',(int)$status); ?></td>
					</tr>
				</table>
			</div>
			</form>
		</div>
	</div>
</div>
<?= $footer; ?>

<?= $this->builder->js('ckeditor'); ?>


<?= $this->builder->js('autocomplete', '#product_list_autocomplete', 'name', 'product_id', 'callback_product_autocomplete'); ?>

<script type="text/javascript">//<!--
$(document).ready(function(){
	$('#product_list').sortable({revert:true});
});

function callback_product_autocomplete(selector, data){
	if($('#product_list').find('.ac_item_id[value=' + data.product_id + ']').length > 0) return;
	
	html =	'<li>';
	html += '	<input type="hidden" class="ac_item_id" name="products[%product_id%][product_id]" value="%product_id%" />';
	html += '	<div class="autocomplete_label">';
	html += '			<input type="text" size="60" name="products[%product_id%][name]" value="%name%" />';
	html += '	</div>';
	html += '	<img onclick="$(this).parent().remove()" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />';
	html += '</li>';
	
	html = html.replace(/%product_id%/g, data.product_id)
							.replace(/%name%/g, data.name);
							
	$('#product_list').append(html);
}
//--></script>

<?= $this->builder->js('errors', $errors); ?>