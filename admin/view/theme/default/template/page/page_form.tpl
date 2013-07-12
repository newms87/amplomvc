<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<?= $this->builder->display_errors($errors); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<a onclick="$('#form').submit()" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			
			<div id="tabs" class="htabs">
				<a href="#tab-general"><?= $tab_general; ?></a>
				<a href="#tab-design"><?= $tab_design; ?></a>
			</div>
			
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
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
							<td><?= $entry_meta_keywords; ?></td>
							<td><textarea name="meta_keywords" rows="4" cols="60"><?= $meta_keywords; ?></textarea></td>
						</tr>
						<tr>
							<td><?= $entry_meta_description; ?></td>
							<td><textarea name="meta_description" rows="8" cols="60"><?= $meta_description; ?></textarea></td>
						</tr>
						<tr>
							<td><?= $entry_content; ?></td>
							<td><textarea name="content" class="ckedit"><?= $content; ?></textarea></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><?= $this->builder->build('select',$data_statuses,'status',(int)$status); ?></td>
						</tr>
					</table>
				</div>
				<div id="tab-design">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_layout; ?></td>
							<td>
								<? $this->builder->set_config('layout_id', 'name');?>
								<div id="layout_select"><?= $this->builder->build('select', $data_layouts, "layout_id", $layout_id); ?></div>
								<a id="create_layout" class="link_button"><?= $button_create_layout; ?></a>
								<span id="create_layout_load" style="display:none"><?= $text_creating_layout; ?></span>
							</td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_store; ?></td>
							<? $this->builder->set_config('store_id', 'name');?>
							<td><?= $this->builder->build('multiselect', $data_stores, "stores", $stores); ?></td>
						</tr>
						<tr>
							<td><?= $entry_blocks; ?></td>
							<td>
								<table id="assigned_block_list" class="list">
									<thead>
										<tr>
											<td><?= $column_block_name; ?></td>
											<td><?= $column_block_store; ?></td>
											<td><?= $column_block_position; ?></td>
										</tr>
									</thead>
									<tbody>
										<tr id="block_template">
											<td>%name%</td>
											<td>%store%</td>
											<td>%position%</td>
										</tr>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="3">
												<a id="add_block" href="<?= $url_blocks; ?>" target="_blank" class="button"><?= $button_add_blocks; ?></a>
											</td>
										</tr>
									</tfoot>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">//<!--
var block_template = $('#block_template')[0].outerHTML;
$('#block_template').remove();

function add_block_item(name, store, position) {
	template = block_template
		.replace(/%name%/g, name)
		.replace(/%store%/g, store)
		.replace(/%position%/g, position);
	
	$('#assigned_block_list tbody').append($(template).attr('id',''));
};

function load_assigned_blocks() {
	$('#assigned_block_list tbody').empty();
	
	url = "<?= $url_load_blocks; ?>";
	
	data = $('[name="stores[]"], [name=layout_id]').serialize(); 
	$.post(url, data, function(json){
		if (json) {
			for (var b in json) {
				add_block_item(json[b]['display_name'], json[b]['store_name'], json[b]['position']);
			}
		}
	}, 'json');
}

$('[name="stores[]"], [name=layout_id]').change(load_assigned_blocks).first().change();
//--></script>

<script type="text/javascript">//<!--
$('#create_layout').click(function(){
	url = "<?= $url_create_layout; ?>";
	
	layout_name = $('[name=name]').val();
	
	if(!layout_name){
		alert('You must specify a name before you can create a new layout!');
		return false;
	}
	
	data = {
		name: layout_name
	};
	
	$('#create_layout_load').show();
	$("#create_layout").hide();
	
	$('#layout_select').load(url, data, function(){
		$('#create_layout_load').hide();
		$('#create_layout').show();
	});
	
	return false;
});
//--></script>

<script type="text/javascript">//<!--
$('#tabs a').tabs();
//--></script>

<?= $this->builder->js('ckeditor'); ?>

<?= $this->builder->js('translations', $translations); ?>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
