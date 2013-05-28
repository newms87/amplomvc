<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs();?>
	<? if($errors){?>
		<div class="message_box warning">
		<? $br=false; foreach($errors as $e){ echo ($br?'<br>':'') . $e; $br=true;}?>
		</div>
	<? }?>
	
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'shipping.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a onclick="location = '<?= $cancel; ?>';" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<div id="tabs" class="htabs"><a href="#tab-general"><?= $tab_general; ?></a><a href="#tab-articles"><?= $tab_article; ?></a></div>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td><span class="required"></span> <?= $entry_name; ?></td>
							<td><input type="text" name="name" value="<?= $name; ?>" size="100" />
							</td>
						</tr>
						<tr>
							<td><span class="required"></span><?= $entry_keyword; ?></td>
							<td>
								<input type="text" onfocus='generate_url_warning(this)' name="keyword" value="<?= $keyword; ?>" />
								<a class='gen_url' onclick='generate_url(this)'><?=$button_generate_url;?></a>
							</td>
						</tr>
						<tr>
							<td><?= $entry_section_attr; ?></td>
							<td><?= $this->builder->build('select',$section_attrs, 'section_attr', (int)$section_attr);?></td>
						</tr>
						<div id="languages" class="htabs">
							<? foreach ($languages as $language) { ?>
							<a href="#language<?= $language['language_id']; ?>"><img src="<?= HTTP_THEME_IMAGE . 'flags/<?= $language['image']; ?>'; ?>" title="<?= $language['name']; ?>" /> <?= $language['name']; ?></a>
							<? } ?>
					</div>
					<tr>
								<td><?= $entry_teaser; ?></td>
								<? foreach ($languages as $language) {?>
									<td id='language<?= $language['language_id'];?>'><input size="80" type='text' name="manufacturer_description[<?= $language['language_id']; ?>][teaser]" value="<?= isset($manufacturer_description[$language['language_id']]) ? $manufacturer_description[$language['language_id']]['teaser'] : ''; ?>" /></td>
								<? } ?>
					</tr>
					<tr>
								<td><?= $entry_description; ?></td>
								<? foreach ($languages as $language) {?>
									<td id='language<?= $language['language_id'];?>'><textarea name="manufacturer_description[<?= $language['language_id']; ?>][description]" class='ckedit' id="description<?= $language['language_id']; ?>"><?= isset($manufacturer_description[$language['language_id']]) ? $manufacturer_description[$language['language_id']]['description'] : ''; ?></textarea></td>
								<? } ?>
					</tr>
					<tr>
								<td><?= $entry_shipping_return; ?></td>
								<? foreach ($languages as $language) {?>
									<td id='language<?= $language['language_id'];?>'><textarea name="manufacturer_description[<?= $language['language_id']; ?>][shipping_return]" class='ckedit' id="ship-ret<?= $language['language_id']; ?>"><?= isset($manufacturer_description[$language['language_id']]) ? $manufacturer_description[$language['language_id']]['shipping_return'] : ''; ?></textarea></td>
								<? } ?>
					</tr>
					<tr>
							<td><?= $entry_store; ?></td>
							<? $this->builder->set_config('store_id', 'name');?>
							<td><?= $this->builder->build('multiselect', $data_stores, "manufacturer_store", $manufacturer_store);?></td>
						</tr>
						<tr>
							<td><?= $entry_image; ?></td>
							<td>
								<?= $this->builder->set_builder_template('click_image');?>
						<?= $this->builder->image_input("image", $image, $thumb);?>
							</td>
						</tr>
						<tr>
							<td><?=$entry_date_active;?></td>
							<td><input type='text' class='datetime' name='date_active' value='<?=$date_active;?>' /></td>
						</tr>
						<tr>
							<td><?=$entry_date_expires;?></td>
							<td><input type='text' class='datetime' name='date_expires' value='<?=$date_expires;?>' /></td>
						</tr>
						<tr>
							<td><?=$entry_status;?></td>
							<td><?= $this->builder->build('select',$statuses, 'status',$status);?></td>
						</tr>
						<tr>
							<td><?=$entry_editable;?></td>
							<td><?= $this->builder->build('select',$yes_no, 'editable',$editable);?></td>
						</tr>
						<tr>
							<td><?= $entry_sort_order; ?></td>
							<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1" /></td>
						</tr>
					</table>
				</div>
				<div id='tab-articles'>
					<table class='list'>
						<thead>
								<tr>
									<td><?= $entry_article_title;?></td>
									<td><?= $entry_article_description;?></td>
									<td><?= $entry_article_link;?></td>
									<td></td>
								</tr>
						</thead>
						<? $row = 0;?>
						<? if($articles)foreach($articles as $article){?>
						<tbody id='article-<?=$row;?>'>
								<tr>
									<td class="left"><input type="text" name="articles[<?=$row;?>][title]" value="<?=$article['title'];?>" /></td>
									<td class="left"><textarea id='article-description-<?=$row;?>' class='ckedit' name="articles[<?=$row;?>][description]" ><?=$article['description'];?></textarea></td>
									<td class="left"><input type="text" name="articles[<?=$row;?>][link]" size='60' value="<?=$article['link'];?>" /></td>
									<td class="left"><a onclick="remove_ckeditor_for($('#article-description-<?=$row;?>'));$('#article-<?=$row;?>').remove();" class="button"><?=$button_remove;?></a></td>
								</tr>
						</tbody>
						<? $row++;?>
						<? }?>
							<tbody>
								<tr>
									<td class="left" colspan="4"></td>
									<td class="center"><a onclick="add_article(this);" class="button"><?=$button_add_article;?></a></td>
									<td class="left" colspan="3"></td>
								</tr>
						</tbody>
					</table>
				</div>
				
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('ckeditor');?>

<script type="text/javascript">//<!--
function generate_url_warning(field){
	if($('#gen_warn').length == 0)
			$(field).parent().append('<span id="gen_warn" style="color:red"><?=$warning_generate_url;?></span>');
}
function generate_url(c){
	$(c).fadeOut(500,function(){$(this).show();});
	$('#gen_warn').remove();
	name =$('input[name="name"]').val();
	if(!name)
			alert("Please make a name for this Designer before generating the URL");
	$.post("<?= HTTP_ADMIN . "index.php?route=catalog/manufacturer/generate_url"; ?>",{manufacturer_id:<?=$manufacturer_id?$manufacturer_id:0;?>,name:name},function(json){$('input[name="keyword"]').val(json);},'json');
}
 //--></script>
<script type="text/javascript">//<!--
var article_row = <?=$row;?>;
function add_article(context){
	html =	'<tbody id="article-%row%">';
	html += '	<tr>';
	html += '			<td class="left"><input type="text" name="articles[%row%][title]" /></td>';
	html += '			<td class="left"><textarea id="article-description-%row%" name="articles[%row%][description]" ></textarea></td>';
	html += '			<td class="left"><input type="text" name="articles[%row%][link]" size="60" /></td>';
	html += '			<td class="left"><a onclick="remove_ckeditor_for($(\'#article-description-%row%\'));$(\'#article-%row%\').remove();" class="button"><?=$button_remove;?></a></td>';
	html += '	</tr>';
	html += '</tbody>';
	$(context).closest('tbody').before(html.replace(/%row%/g,article_row));
	init_ckeditor_for($('#article-description-'+article_row));
	article_row++;
}
//--></script>

<script type="text/javascript">//<!--
$('#tabs a').tabs();
$('#languages a').tabs();
//--></script>

<?= $this->builder->js('datepicker');?>
<?= $this->builder->js('errors', $errors);?>

<?= $footer; ?>