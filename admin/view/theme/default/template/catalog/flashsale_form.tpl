<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons">
				<? if(isset($preview)){?>
						<a href="<?= $preview; ?>" target="_blank" class="button"><?= $button_preview; ?></a>
				<? }?>
				<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="content">
			<div id="tabs" class="htabs"><a href="#tab-general"><?= $tab_general; ?></a><a href="#tab-article"><?= $tab_article; ?></a></div>
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
					<tr>
							<td><?= $entry_extend_flashsale; ?></td>
							<td><?= $this->builder->build('select',$data_yes_no,'extend_flashsale',(int)$extend_flashsale); ?></td>
					</tr>
					<tr>
							<td><?= $entry_designer; ?></td>
							<td><?= $this->builder->build('select',$designer_list,'','',array('id'=>'autofill_designer')); ?><a onclick='autofill_designer_info();' class='button'><?= $button_autofill; ?></a></td>
					</tr>
					<tr>
							<td class="required"> <?= $entry_discount; ?></td>
							<td><input type="text" name="discount" value="<?= $discount; ?>" size="20" />
									<?= $this->builder->build('select',$data_discount_types, "discount_type",$discount_type); ?></td>
					</tr>
					<tr>
							<td class="required"> <?= $entry_name; ?></td>
							<td><input id='flashsale_title' type="text" name="name" value="<?= $name; ?>" size="40" /></td>
					</tr>
					<tr>
							<td class="required"> <?= $entry_keyword; ?></td>
							<td>
								<input id='flashsale_keyword' onfocus='generate_url_warning(this)' type="text" name="keyword" value="<?= $keyword; ?>" size="40" />
								<a class='gen_url' onclick='generate_url(this)'><?= $button_generate_url; ?></a>
							</td>
					</tr>
					<tr>
						<td><?= $entry_designers; ?></td>
						<td><ul id='designer_list'><? if(isset($designers))
							foreach($designers as $d){
									echo "<li>";
									echo $this->builder->build('select',$designer_list, 'designers[]',(int)$d);
									echo "<a onclick='$(this).parent().remove()'>remove</a>";
							}?>
							</ul>
							<a onclick="add_designer();"><?= $button_add_designer; ?></a>
						</td>
					</tr>
					<tr>
							<td><?= $entry_teaser; ?></td>
							<td><input type="text" name="teaser" value="<?= $teaser; ?>" size="40" /></td>
					</tr>
					<tr>
							<td><?= $entry_teaser; ?></td>
							<td><textarea id='flashsale_teaser' class='ckedit' name="teaser"><?= $teaser; ?></textarea></td>
					</tr>
					<tr>
						<td><?= $entry_product; ?></td>
						<td><input type="text" name="product" value="" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>
								<ol id="featured-product" class="scrollbox p_product_list">
								<? foreach ($products as $product) { ?>
										<li id="featured-product<?= $product['product_id']; ?>">
											<span class='p_name'><?= $product['name']; ?></span>
											<span class='p_price'>$<input type="text" name="products[<?= $product['product_id']; ?>][price]" value="<?= $product['price']; ?>" /></span>
											<input type="hidden" name="products[<?= $product['product_id']; ?>][name]" value="<?= $product['name']; ?>" />
											<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" onclick="$(this).parent().remove()"/>
										</li>
								<? } ?>
								</ol>
								<a style='margin-top:10px;display:block' onclick="$('#featured-product').empty();"><?= $button_clear_products; ?></a>
							</td>
					</tr>
						<tr>
							<td><?= $entry_image; ?></td>
								<td>
									<?= $this->builder->set_builder_template('click_image'); ?>
									<?= $this->builder->image_input("image", $image, $thumb); ?>
							</td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_date_start; ?></td>
							<td><input type="text" class="datetime" name="date_start" value="<?= $date_start; ?>" size="20" /></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_date_end; ?></td>
							<td><input type="text" class="datetime" name="date_end" value="<?= $date_end; ?>" size="20" /></td>
						</tr>
						<tr>
							<td><?= $entry_section_attr; ?></td>
							<td><?= $this->builder->build('select',$section_attrs, 'section_attr',$section_attr); ?></td>
						</tr>
						<tr>
							<td><?= $entry_customer_group; ?></td>
							<td><?= $this->builder->build('select',$customer_groups,'customer_group_id',$customer_group_id); ?></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><?= $this->builder->build('select',$statuses, 'status',(int)$status); ?></td>
						</tr>
					</table>
				</div>
				<div id='tab-article'>
					<table class='list'>
						<thead>
								<tr>
									<td><?= $entry_article_title; ?></td>
									<td><?= $entry_article_description; ?></td>
									<td><?= $entry_article_link; ?></td>
									<td></td>
								</tr>
						</thead>
						<? if($articles)foreach($articles as $row=>$article){?>
						<tbody id='article-<?= $row; ?>'>
								<tr>
									<input type='hidden' name="articles[<?= $row; ?>][article_id]" value="<?= $article['article_id']; ?>" />
									<td class="left"><input type="text" name="articles[<?= $row; ?>][title]" value="<?= $article['title']; ?>" /></td>
									<td class="left"><textarea class='ckedit' id='article-description-<?= $row; ?>' name="articles[<?= $row; ?>][description]" ><?= $article['description']; ?></textarea></td>
									<td class="left"><input type="text" name="articles[<?= $row; ?>][link]" size='60' value="<?= $article['link']; ?>" /></td>
									<td class="left"><a onclick="remove_ckeditor_for($('#article-description-<?= $row; ?>'));$('#article-<?= $row; ?>').remove();" class="button"><?= $button_remove; ?></a></td>
								</tr>
						</tbody>
						<? }?>
							<tbody>
								<tr>
									<td class="left" colspan="4"></td>
									<td class="center"><a onclick="add_article(this);" class="button"><?= $button_add_article; ?></a></td>
									<td class="left" colspan="3"></td>
								</tr>
						</tbody>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

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
			alert("Please make a name for this Flashsale before generating the URL");
	$.post("<?= HTTP_ADMIN . "index.php?route=catalog/flashsale/generate_url"; ?>",{flashsale_id:<?= $flashsale_id; ?>,name:name},function(json){$('input[name="keyword"]').val(json);},'json');
}
 //--></script>
<script type="text/javascript"><!--
function autofill_designer_info(){
	$.post("<?= HTTP_ADMIN . "index.php?route=catalog/flashsale/get_designer_info"; ?>",{designer_id:$('#autofill_designer').val()},apply_designer_info,'json');
}
function apply_designer_info(data){
	if(!data)return;
	for(p=0;p<data.products.length;p++){
			add_featured_product(data.products[p].product_id,data.products[p].name,data.products[p].price);
	}
	add_designer(data.designer_id);
	$('#flashsale_title').val(data.name);
	$('#image').val(data.image);
	$('#thumb').attr('src',data.thumb);
	remove_ckeditor_for($('#flashsale_teaser'));
	$('#flashsale_teaser').val(data.description);
	init_ckeditor_for($('#flashsale_teaser'));
	$('input[name="keyword"]+a.gen_url').click();
}

function add_featured_product(id,name,price){
			$('#featured-product' + id).remove();
			discount = $('input[name="discount"]').val();
			discount_type = $('select[name="discount_type"]').val();
			price = discount_type == 'percent'?price - ((discount/100)*price):price-discount;
			price = price.toFixed(2);
			html = '<li id="featured-product' + id + '">';
			html +=	'<span class="p_name">' + name + '</span>';
			html +=	'<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" onclick="$(this).parent().remove()"/>';
			html +=	'<span class="p_price">$<input type="text" name="products['+id+'][price]" value="'+price+'" /></span>';
			html +=	'<input type="hidden" name="products['+id+'][name]" value="'+name+'" />';
			html +='</li>';
			$('#featured-product').append(html);
}
--></script>

<script type="text/javascript">//<!--
$(document).ready(function(){
	$('#featured-product').sortable({revert:true});
});
//--></script>


<script type='text/javascript'>//<!--
function add_designer(id){
	html = '<li>' + "<?= $this->builder->build('select',$designer_list,'designers[]'); ?>" + '<a onclick="$(this).parent().remove();">remove</a></li>';
	$('#designer_list').append(html);
	if(id)
			$('#designer_list li:last select').val(id);
}
//--></script>

<?= $this->builder->js('ckeditor'); ?>
<script type="text/javascript">//<!--
var article_row = <?= count($articles)+1; ?>;
function add_article(context){
	html =	'<tbody id="article-%row%">';
	html += '	<tr>';
	html += '	<input type="hidden" name="articles[%row%][article_id]" value="1" /></td>';
	html += '			<td class="left"><input type="text" name="articles[%row%][title]" /></td>';
	html += '			<td class="left"><textarea id="article-description-%row%" name="articles[%row%][description]" ></textarea></td>';
	html += '			<td class="left"><input type="text" name="articles[%row%][link]" size="60" /></td>';
	html += '			<td class="left"><a onclick="remove_ckeditor_for($(\'#article-description-%row%\'));$(\'#article-%row%\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html += '	</tr>';
	html += '</tbody>';
	$(context).closest('tbody').before(html.replace(/%row%/g,article_row));
	init_ckeditor_for($('#article-description-'+article_row));
	article_row++;
}
//--></script>
<script type="text/javascript">//<!--

$('input[name=\'product\']').autocomplete({
	delay: 0,
	source: function(request, response) {
			$.ajax({
				url: "<?= HTTP_ADMIN . "index.php?route=catalog/product/autocomplete"; ?>" + '&filter_name=' +	encodeURIComponent(request.term),
				dataType: 'json',
				success: function(json) {
						response($.map(json, function(item) {
							return {
									label: item.name,
									value: item.product_id,
									price: item.price
							}
						}));
				}
			});
			
	},
	select: function(event, ui) {
			add_featured_product(ui.item.value, ui.item.label, ui.item.price);
			return false;
	}
});
//--></script>

<?= $this->builder->js('datepicker'); ?>

<script type="text/javascript">//<!--
$('input[name="date_start"]').change(function(){
	start = new Date($(this).val());
	start.setDate(start.getDate()+3);
	hours = start.getHours();
	mins = start.getMinutes();
	$('input[name="date_end"]').val($.datepicker.formatDate('yy-mm-dd',start)+' '+hours+':'+mins);
});
//--></script>

<?= $this->builder->js('errors', $errors); ?>
<script type="text/javascript">//<!--
$('#tabs a').tabs();
//--></script>

<?= $footer; ?>