<?= $header; ?>
<div class="content" id='mail_newsletter'>
	<?= $this->builder->display_breadcrumbs(); ?>
	<?= $this->builder->display_errors($errors); ?>
<div class="box">
	<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
		<div class="buttons">
			<a onclick="prepare_preview();$.post('<?= $preview; ?>', $('#form').serialize(), handle_preview, 'html');" class="button"><?= $button_preview; ?></a>
			<a onclick="$('#form').submit();" class="button save_form"><?= $button_save; ?></a>
			<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
		</div>
	</div>
	<div class="content">
		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
			<table class="form">
				<tr>
					<td><?= $entry_url; ?></td>
					<td>
						<? if(!empty($url_active)) { ?>
						<a target="_blank" href="<?= $url_active; ?>"><?= $url_active; ?></a>
						<? } else { ?>
						<a onclick="$('.buttons .save_form').click()"><?= $text_no_url_active; ?></a>
						<? } ?>
					</td>
				</tr>
				<tr>
					<td><?= $entry_name; ?></td>
					<td><input type="text" name="name" value="<?= $name; ?>" size="60"/></td>
				</tr>
				<tr>
					<td><?= $entry_send_date; ?></td>
					<td><input type="text" name="send_date" class='datetime' value="<?= $send_date; ?>" /></td>
				</tr>
				<tr>
					<td>
							<div><?= $entry_featured; ?></div>
							<div>
								<?= $this->builder->set_config('manufacturer_id', 'name'); ?>
								<?= $this->builder->build('select', $data_designers, "newsletter[featured][designer][designer_id]", !empty($newsletter) ? $newsletter['featured']['designer']['designer_id'] : '', array('id'=>'designer_select')); ?>
							</div>
							<div>
								<?= $this->builder->set_config('product_id', 'name'); ?>
								<?= $this->builder->build('select', $data_designer_products, "newsletter[featured][product][product_id]", !empty($newsletter) ? $newsletter['featured']['product']['product_id'] : '', array('id'=>'product_select')); ?>
							</div>
					</td>
					<td>
							<div id='newsletter_featured'>
								<div class='product_image'>
										<div>
											<?= $this->builder->image_input("newsletter[featured][product][image]", !empty($newsletter) ? $newsletter['featured']['product']['image'] : ''); ?>
										</div>
										<div class='image_heading'>
											<input type="text" name="newsletter[featured][product][name]" value="<?= !empty($newsletter) ? $newsletter['featured']['product']['name'] : ''; ?>" />
										</div>
										<div>
											<input type="text" size="3" name="newsletter[featured][product][width]" value="<?= !empty($newsletter) ? $newsletter['featured']['product']['width'] : ''; ?>" />
											x
											<input type="text" size="3" name="newsletter[featured][product][height]" value="<?= !empty($newsletter) ? $newsletter['featured']['product']['height'] : ''; ?>" />
										</div>
										<div style="margin-top:10px"><?= $entry_featured_product_image; ?></div>
								</div>
								<div class='designer_image'>
										<div>
											<?= $this->builder->image_input("newsletter[featured][designer][image]", !empty($newsletter) ? $newsletter['featured']['designer']['image'] : ''); ?>
										</div>
										<div class='image_heading'>
											<input type="text" name="newsletter[featured][designer][name]" value="<?= !empty($newsletter) ? $newsletter['featured']['designer']['name'] : ''; ?>" />
										</div>
										<div>
											<input type="text" size="3" name="newsletter[featured][designer][width]" value="<?= !empty($newsletter) ? $newsletter['featured']['designer']['width'] : ''; ?>" />
											x
											<input type="text" size="3" name="newsletter[featured][designer][height]" value="<?= !empty($newsletter) ? $newsletter['featured']['designer']['height'] : ''; ?>" />
										</div>
										<div style="margin-top:10px"><?= $entry_featured_designer_image; ?></div>
								</div>
								<div class='featured_info'>
										<div>
											<label for='designer_title'><?= $entry_designer_title; ?></label>
											<input type="text" name="newsletter[featured][designer][title]" value="<?= !empty($newsletter) ? $newsletter['featured']['designer']['title'] : ''; ?>" />
										</div>
										<div>
											<label for='designer_description'><?= $entry_designer_description; ?></label>
											<textarea name="newsletter[featured][designer][description]" class='ckedit'><?= !empty($newsletter) ? $newsletter['featured']['designer']['description'] : ''; ?></textarea>
										</div>
										<div>
											<label for="designer_article"><?= $entry_designer_article; ?></label>
											<input type="text" name="newsletter[featured][designer][article]" value="<?= !empty($newsletter) ? $newsletter['featured']['designer']['article'] : ''; ?>" />
										</div>
								</div>
							</div>
					</td>
				</tr>
				<tr>
					<td>
						<div><?= $entry_product_list; ?></div>
						<div><input type="text" id='product_list_autocomplete' filter="filter_name" route="catalog/product/autocomplete&filter_status=1" /></div>
						<div><?= $text_autocomplete; ?></div>
					</td>
					<td>
						<ol id="product_list" class="scrollbox editable_list">
							<? if(!empty($newsletter['products'])) { ?>
							<? foreach ($newsletter['products'] as $product) { ?>
							<li>
								<div class='editable_label'>
										<input type="hidden" class='ac_item_id' name="newsletter[products][<?= $product['product_id']; ?>][product_id]" value="<?= $product['product_id']; ?>" />
										<input type="text" size="60" name="newsletter[products][<?= $product['product_id']; ?>][name]" value="<?= $product['name']; ?>" />
								</div>
								<img onclick="$(this).parent().remove()" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />
							</li>
							<? } ?>
							<? } ?>
						</ol>
					</td>
				</tr>
				<tr>
					<td>
						<div><?= $entry_designer_list; ?></div>
						<div><input type="text" id='designer_list_autocomplete' filter="name" route="catalog/manufacturer/autocomplete&status=1" /></div>
						<div><?= $text_autocomplete; ?></div>
					</td>
					<td>
						<ol id="designer_list" class="scrollbox editable_list">
							<? if(!empty($newsletter['designers'])) { ?>
							<? foreach ($newsletter['designers'] as $designer) { ?>
							<li>
								<input type="hidden" class='ac_item_id' name="newsletter[designers][<?= $designer['designer_id']; ?>][designer_id]" value="<?= $designer['designer_id']; ?>" />
								<div class='editable_label'>
										<input type="text" name="newsletter[designers][<?= $designer['designer_id']; ?>][name]" value="<?= $designer['name']; ?>" />
								</div>
								<img onclick="$(this).parent().remove()" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />
							</li>
							<? } ?>
							<? } ?>
						</ol>
					</td>
				</tr>
				<tr>
					<td><?= $entry_article_list; ?></td>
					<td>
						<div>
							<div><?= $entry_article_list_image; ?></div>
							<?= $this->builder->image_input("newsletter[articles_image]", !empty($newsletter['articles_image']) ? $newsletter['articles_image'] : ''); ?>
							<span><?= $entry_article_list_url; ?></span>
							<input type="text" name="newsletter[articles_url]" value="<?= !empty($newsletter['articles_url']) ? $newsletter['articles_url'] : ''; ?>"size="50" />
						</div>
						<div style='margin-top:10px;'><?= $entry_article_list_articles; ?></div>
						<div id='add_article_form'>
							<label for="add_article_title"><?= $entry_article_title; ?></label><input type="text" id="add_article_title" size="30"/>
							<label for="add_article_href"><?= $entry_article_href; ?></label><input type="text" id="add_article_href" size="80" />
							<input type="button" value="Add Article" class="button" id="add_article_button" />
						</div>
						<div>
							<ol id="article_list" class="scrollbox editable_list">
								<? $article_row = 1;?>
								<? if(!empty($newsletter['articles'])) { ?>
								<? foreach ($newsletter['articles'] as $article) { ?>
								<li>
										<div class='editable_label'>
											<input type="text" name="newsletter[articles][<?=$article_row?>][title]" value="<?= $article['title']; ?>" size="30"/>
											<input type="text" name="newsletter[articles][<?= $article_row; ?>][href]" value="<?= $article['href']; ?>"size="50" />
										</div>
										<img onclick="$(this).parent().remove()" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />
								</li>
								<? $article_row++;?>
								<? } ?>
								<? } ?>
							</ol>
						</div>
					</td>
				</tr>
				<tr>
					<td><?= $entry_featured_article_list; ?></td>
					<td>
						<div id='add_featured_article_form'>
							<?= $this->builder->image_input("", ''); ?>
							<label for="add_featured_article_title"><?= $entry_article_title; ?></label><input type="text" id="add_featured_article_title" size="30"/>
							<label for="add_featured_article_teaser"><?= $entry_article_teaser; ?></label><input type="text" id="add_featured_article_teaser" size="30"/>
							<label for="add_featured_article_href"><?= $entry_article_href; ?></label><input type="text" id="add_featured_article_href" size="80" />
							<input type="button" value="Add Featured Article" class="button" id="add_featured_article_button" />
						</div>
						<div>
							<ol id="featured_article_list" class="scrollbox large editable_list">
								<? $featured_article_row = 1;?>
								<? if(!empty($newsletter['featured']['articles'])) { ?>
								<? foreach ($newsletter['featured']['articles'] as $article) { ?>
								<li>
										<div class='editable_label'>
											<?= $this->builder->image_input("newsletter[featured][articles][$featured_article_row][image]", $article['image']); ?>
											<input type="text" name="newsletter[featured][articles][<?=$featured_article_row?>][title]" value="<?= $article['title']; ?>" size="30" />
											<input type="text" name="newsletter[featured][articles][<?=$featured_article_row?>][teaser]" value="<?= $article['teaser']; ?>" size="30"/>
											<input type="text" name="newsletter[featured][articles][<?= $featured_article_row; ?>][href]" value="<?= $article['href']; ?>" size="80" />
										</div>
										<img onclick="$(this).parent().remove()" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />
								</li>
								<? $featured_article_row++;?>
								<? } ?>
								<? } ?>
							</ol>
						</div>
					</td>
				</tr>
			</table>
		</form>
	</div>
</div>

<script type="text/javascript">//<!--

designer_info = <?= json_encode($data_designers); ?>;

$('#designer_select').change(function(event, first_time){
	name_field = $('[name="newsletter[featured][designer][name]"]');
	image_field = $('[name="newsletter[featured][designer][image]"]');
	thumb = image_field.siblings('img');
		
	if(!$(this).val()){
			name_field.val('');
			image_field.val('');
			image_field.siblings('a:last').click();
			return;
	}
	
	filter = {filter_manufacturer_id: $(this).val()};
	
	if(!first_time){
			name_field.val($(this).find('option:selected').html());
	}
	
	for(i=0; i<designer_info.length; i++){
			if(designer_info[i].manufacturer_id == $(this).val()){
				data = designer_info[i];
				break;
			}
	}
	
	if(typeof addSingleImage == 'function' && !first_time)
			addSingleImage(data['image'], image_field.attr('id'), thumb.attr('id'));
		
	$.post("<?= HTTP_ADMIN . "index.php?route=catalog/product/select"; ?>", {filter: filter, select: $('#product_select').val(), fields: 'image'},
			function(json){
				$('#product_select').html(json['html']).data(json['option_data']);
				if(!first_time){
						$('#product_select').change();
				}
			},'json');
}).trigger('change', true);

$('#product_select').change(function(){
	name_field = $('[name="newsletter[featured][product][name]"]');
	image_field = $('[name="newsletter[featured][product][image]"]');
	thumb = image_field.siblings('img');
	
	if(!$(this).val()){
			name_field.val('');
			image_field.val('');
			image_field.siblings('a:last').click();
			return;
	}
	
	option = $('#product_select option:selected');
	
	name_field.val(option.html());
	
	data = $(this).data(option.val());
	
	addSingleImage(data['image'], image_field.attr('id'), thumb.attr('id'));
});

$(document).ready(function(){
	$('#article_list').sortable({revert:true});
});

var article_row = <?= $article_row; ?>;
$('#add_article_button').click(function(){
	if(!$('#add_article_title').val() || !$('#add_article_href').val()) return;
	
	html =	'<li>';
	html += '	<div class="editable_label">';
	html += '			<input type="text" name="newsletter[articles][%row%][title]" value="%title%" />';
	html += '			<input type="text" name="newsletter[articles][%row%][href]" value="%href%" />';
	html += '	</div>';
	html += '	<img onclick="$(this).parent().remove()" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />';
	html += '</li>';
	
	html = html.replace(/%row%/g, 'new'+article_row)
							.replace(/%title%/g, $('#add_article_title').val())
							.replace(/%href%/g, $('#add_article_href').val());
							
	$('#article_list').append(html);
	
	$('#add_article_form input[type=text]').val('');
	
	article_row++;
});
//--></script>
<script type="text/javascript">//<!--
$(document).ready(function(){
	$('#featured_article_list').sortable({revert:true});
});
var featured_article_row = <?= $featured_article_row; ?>;
$('#add_featured_article_button').click(function(){
	if(!$('#add_featured_article_title').val() || !$('#add_featured_article_href').val()) return;
	
	html =	'<li>';
	html += '	<div class="editable_label">';
	html += "			<?= $this->builder->image_input("newsletter[featured][articles][%row%][image]", '%image%', null, null, null, null, true); ?>";
	html += '			<input type="text" name="newsletter[featured][articles][%row%][title]" value="%title%" />';
	html += '			<input type="text" name="newsletter[featured][articles][%row%][teaser]" value="%teaser%" />';
	html += '			<input type="text" name="newsletter[featured][articles][%row%][href]" value="%href%" size="80"/>';
	html += '	</div>';
	html += '	<img onclick="$(this).parent().remove()" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />';
	html += '</li>';
	
	html = html.replace(/%row%/g, 'new'+featured_article_row)
							.replace(/%image%/g, $('#add_featured_article_form .image input').val())
							.replace(/%title%/g, $('#add_featured_article_title').val())
							.replace(/%teaser%/g, $('#add_featured_article_teaser').val())
							.replace(/%href%/g, $('#add_featured_article_href').val());
	
	$('#featured_article_list').append(html);
	
	$('#featured_article_list').children().last().find('.image img').attr('src',$('#add_featured_article_form .image img').attr('src'));
	
	$('#add_featured_article_form input[type=text]').val('');
	$('#add_featured_article_form .image a + a').click();
	
	featured_article_row++;
});
//--></script>

<?= $this->builder->js('autocomplete', '#product_list_autocomplete', 'name', 'product_id', 'callback_product_autocomplete'); ?>

<?= $this->builder->js('autocomplete', '#designer_list_autocomplete', 'name', 'manufacturer_id', 'callback_designer_autocomplete'); ?>

<script type="text/javascript">//<!--
$(document).ready(function(){
	$('#product_list, #designer_list').sortable({revert:true});
});

function callback_product_autocomplete(selector, data){
	if($('#product_list').find('.ac_item_id[value=' + data.product_id + ']').length > 0) return;
	
	html =	'<li>';
	html += '	<input type="hidden" class="ac_item_id" name="newsletter[products][%product_id%][product_id]" value="%product_id%" />';
	html += '	<div class="autocomplete_label">';
	html += '			<input type="text" name="newsletter[products][%product_id%][name]" value="%name%" />';
	html += '	</div>';
	html += '	<img onclick="$(this).parent().remove()" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />';
	html += '</li>';
	
	html = html.replace(/%product_id%/g, data.product_id)
							.replace(/%name%/g, data.name);
							
	$('#product_list').append(html);
}

function callback_designer_autocomplete(selector, data){
	if($('#designer_list').find('.ac_item_id[value=' + data.manufacturer_id + ']').length > 0) return;
	
	html =	'<li>';
	html += '	<input type="hidden" class="ac_item_id" name="newsletter[designers][%manufacturer_id%][designer_id]" value="%manufacturer_id%" />';
	html += '	<div class="autocomplete_label">';
	html += '			<input type="text" name="newsletter[designers][%manufacturer_id%][name]" value="%name%" />';
	html += '	</div>';
	html += '	<img onclick="$(this).parent().remove()" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />';
	html += '</li>';
	
	html = html.replace(/%manufacturer_id%/g, data.manufacturer_id)
							.replace(/%name%/g, data.name);
							
	$('#designer_list').append(html);
}
//--></script>

<div id="preview_page">
	<div id="preview_window">
	</div>
</div>

<script type="text/javascript">//!<--
function prepare_preview(){
	$('<span id="preview_shade"></span>').appendTo('body #container');
	
	shade = $('#preview_shade');
	shade.height(shade.parent().height());
	shade.width(shade.parent().width());
	
	$('#preview_page #preview_window').html('<div style="margin-top:20%;text-align:center">Loading Preview</div><div style="text-align:center"><img src="/admin/<?= HTTP_THEME_IMAGE . 'loading_bar.gif'; ?>" /></div>');
	$('#preview_page').fadeIn(500);
}

function handle_preview(html, textStatus){
	content = $(html).get(1);
	
	$('#preview_window').fadeIn(500).html(html);
	
	shade.click(function(){
		$('#preview_window').html('');
		$('#preview_page').hide();
			$(this).remove();
	});
	
}
//--></script>

<?= $this->builder->js('ckeditor'); ?>

<?= $this->builder->js('datepicker'); ?>

<?= $this->builder->js('errors'); ?>

<?= $footer; ?>