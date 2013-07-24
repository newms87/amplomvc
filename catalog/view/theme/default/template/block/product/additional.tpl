<div id="product_additional_tabs" class="htabs">
	<? if($information){?>
	<a href="#tab-information"><?= $tab_information; ?></a>
	<? }?>
	
	<a href="#tab-shipping-return"><?= $tab_shipping_return; ?></a>
	
	<? if (!empty($attribute_groups)) { ?>
	<a href="#tab-attribute"><?= $tab_attribute; ?></a>
	<? } ?>
</div>

<? if ($information) { ?>
<div id="tab-information" class="tab-content"><?= $information; ?></div>
<? }?>

<div id="tab-shipping-return" class="tab-content">
	<? if ($shipping_policy) { ?>
		<div class="shipping_policy">
			<div class="title"><?= $shipping_policy['title']; ?></div>
			<div class="description"><?= $shipping_policy['description']; ?></div>
		</div>
	<? } ?>
	
	<? if ($return_policy) { ?>
		<div class="return_policy">
			<div class="title"><?= $return_policy['title']; ?></div>
			<div class="description"><?= $return_policy['description']; ?></div>
		</div>
	<? } ?>
	
	<? if (!empty($is_final_explanation)) { ?>
		<p class="final_sale_explain"><?= $is_final_explanation; ?></p>
	<? } ?>
	
	<?= $text_view_policies; ?>
</div>

<? if (!empty($attribute_groups)) { ?>
<div id="tab-attribute" class="tab-content">
	<table class="attribute">
		<? foreach ($attribute_groups as $attribute_group) { ?>
		<thead>
			<tr>
				<td colspan="2"><?= $attribute_group['name']; ?></td>
			</tr>
		</thead>
		<tbody>
			<? foreach ($attribute_group['attributes'] as $attribute) { ?>
			<tr>
				<td><?= $attribute['name']; ?></td>
				<td><?= $attribute['text']; ?></td>
			</tr>
			<? } ?>
		</tbody>
		<? } ?>
	</table>
</div>
<? } ?>

<script type="text/javascript">//<!--
$('#product_additional_tabs a').tabs();
//--></script>