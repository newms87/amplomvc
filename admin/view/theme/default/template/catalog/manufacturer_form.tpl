<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'shipping.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<div id="tabs" class="htabs">
				<a href="#tab-general"><?= $tab_general; ?></a>
			</div>
			<form action="<?= $action; ?>" method="post" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_name; ?></td>
							<td><input type="text" name="name" value="<?= $name; ?>" size="100" />
							</td>
						</tr>
						<tr>
							<td class="required"><?= $entry_keyword; ?></td>
							<td>
								<input type="text" onfocus='generate_url_warning(this)' name="keyword" value="<?= $keyword; ?>" />
								<a class='gen_url' onclick='generate_url(this)'><?= $button_generate_url; ?></a>
							</td>
						</tr>
						<tr>
							<td><?= $entry_teaser; ?></td>
							<td><input type='text' name="teaser" value="<?= $teaser; ?>" size="80" /></td>
						</tr>
						<tr>
							<td><?= $entry_description; ?></td>
							<td><textarea name="description" class="ckedit" /><?= $description; ?></textarea></td>
						</tr>
						<tr>
							<td><?= $entry_shipping_return; ?></td>
							<td><textarea name="shipping_return" class="ckedit" /><?= $shipping_return; ?></textarea></td>
						</tr>
						<tr>
							<td><?= $entry_store; ?></td>
							<td>
								<? $this->builder->set_config('store_id', 'name');?>
								<?= $this->builder->build('multiselect', $data_stores, "stores", $stores); ?>
							</td>
						</tr>
						<tr>
							<td><?= $entry_image; ?></td>
							<td>
								<?= $this->builder->set_builder_template('click_image'); ?>
								<?= $this->builder->image_input("image", $image); ?>
							</td>
						</tr>
						<tr>
							<td><?= $entry_date_active; ?></td>
							<td><input type='text' class='datetime' name='date_active' value='<?= $date_active; ?>' /></td>
						</tr>
						<tr>
							<td><?= $entry_date_expires; ?></td>
							<td><input type='text' class='datetime' name='date_expires' value='<?= $date_expires; ?>' /></td>
						</tr>
						<tr>
							<td><?= $entry_sort_order; ?></td>
							<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1" /></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><?= $this->builder->build('select',$data_statuses, 'status',$status); ?></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('ckeditor'); ?>

<script type="text/javascript">//<!--
function generate_url_warning(field){
	if($('#gen_warn').length == 0)
			$(field).parent().append('<span id="gen_warn" style="color:red"><?= $warning_generate_url; ?></span>');
}
function generate_url(c){
	$(c).fadeOut(500,function(){$(this).show();});
	$('#gen_warn').remove();
	name =$('input[name="name"]').val();
	if(!name)
			alert("Please make a name for this Designer before generating the URL");
	$.post("<?= HTTP_ADMIN . "index.php?route=catalog/manufacturer/generate_url"; ?>",{manufacturer_id:<?= $manufacturer_id?$manufacturer_id:0; ?>,name:name},function(json){$('input[name="keyword"]').val(json);},'json');
}
 //--></script>

<script type="text/javascript">//<!--
$('#tabs a').tabs();
$('#languages a').tabs();
//--></script>

<?= $this->builder->js('datepicker'); ?>
<?= $this->builder->js('errors', $errors); ?>
<?= $this->builder->js('translations', $translations); ?>

<?= $footer; ?>