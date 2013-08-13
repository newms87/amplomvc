<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'order.png'; ?>" alt="" /> <?= $head_title; ?></h1>
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
						<td><input type="text" name="name" value="<?= $name; ?>" /></td>
					</tr>
					<tr>
						<td><?= $entry_sort_order; ?></td>
						<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1" /></td>
					</tr>
					<tr>
						<td><?= $entry_attributes; ?></td>
						<td>
							<table id="attribute_list" class="list">
								<thead>
									<tr>
										<td class="center"><?= $entry_attribute_name; ?></td>
										<td class="center"><?= $entry_attribute_sort_order; ?></td>
										<td></td>
									</tr>
								</thead>
								<tbody>
									
									<?//TODO: find a good way to make template_row w/ translations! ?>
									<? $attributes['template_row'] = array(
										'attribute_id' => '%attribute_id%',
										'name' => '%name%',
										'sort_order' => '%sort_order%',
										'translations' => array(),
									); ?>
									
									<? foreach ($attributes as $key => $attribute) { ?>
										<? $row = $attribute['attribute_id']; ?>
										<tr class="attribute <?= $key; ?>">
											<td class="center">
												<input type="hidden" name="attributes[<?= $row; ?>][attribute_id]" value="<?= $row; ?>" />
												<input type="text" name="attributes[<?= $row; ?>][name]" value="<?= $attribute['name']; ?>" />
											</td>
											<td class="center"><input type="text" class="sort_order" name="attributes[<?= $row; ?>][sort_order]" value="<?= $attribute['sort_order']; ?>" /></td>
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
var list_template = $('#attribute_list').find('.template_row');
var attributes_template = list_template.html();
list_template.remove();

var attr_row = 0;

$('#add_attribute').click(function(){
	template = attributes_template
		.replace(/%attribute_id%/g, 'new_' + attr_row)
		.replace(/%name%/g, '')
		.replace(/%sort_order%/g, 0)
		.replace(/%t_name%/g, '');
	
	$('#attribute_list').append($('<tr class="attribute new_' + attr_row + '" />').append(template));
	
	attr_row++;
});

$('#attribute_list tbody').sortable({cursor: 'move', stop: function() {
	count = 0;
	$('#attribute_list .attribute .sort_order').each(function(i,e){
		$(e).val(count++);
	});
}});
//--></script>

<?= $this->builder->js('errors',$errors); ?>

<? foreach ($attributes as $attribute) { ?>
	<?= $this->builder->js('translations', $attribute['translations'], "attributes[$attribute[attribute_id]][%name%]"); ?>
<? } ?>

<?= $footer; ?>