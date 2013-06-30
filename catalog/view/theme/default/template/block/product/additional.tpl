<div id="product_additional_tabs" class="htabs">
	<? if($information){?>
	<a href="#tab-information"><?= $tab_information; ?></a>
	<? }?>
	
	<? if ($shipping_return) { ?>
	<a href="#tab-shipping"><?= $tab_shipping; ?></a>
	<? } ?>
	
	<? if (!empty($attribute_groups)) { ?>
	<a href="#tab-attribute"><?= $tab_attribute; ?></a>
	<? } ?>
</div>

<? if ($information) { ?>
<div id="tab-information" class="tab-content"><?= $information; ?></div>
<? }?>

<? if ($shipping_return) { ?>
<div id="tab-shipping" class="tab-content">
	<?= $shipping_return; ?><br />
	<?= $is_final?$final_sale_explanation:''; ?><br /><br /><br />
	<?= $shipping_return_link; ?>
</div>
<? } ?>

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