<?= $common_header; ?>
<div class="section" id="mail_newsletter">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'module.png'; ?>" alt=""/> <?= _l("Newsletter"); ?></h1>

			<div class="buttons">
				<a onclick="prepare_preview();$.post('<?= $preview; ?>', $('#form').serialize(), handle_preview, 'html');"
					class="button"><?= _l("Preview"); ?></a>
				<a onclick="$('#form').submit();" class="button save_form"><?= _l("Save"); ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td><?= _l("Newsletter URL:"); ?></td>
						<td>
							<? if (!empty($url_active)) { ?>
								<a target="_blank" href="<?= $url_active; ?>"><?= $url_active; ?></a>
							<? } else { ?>
								<a onclick="$('.buttons .save_form').click()"><?= _l("Please Save this form to view the URL for this newsletter!"); ?></a>
							<? } ?>
						</td>
					</tr>
					<tr>
						<td><?= _l("Newsletter Title:"); ?></td>
						<td><input type="text" name="name" value="<?= $name; ?>" size="60"/></td>
					</tr>
					<tr>
						<td><?= _l("Send Date:"); ?></td>
						<td><input type="text" name="send_date" class="datetimepicker" value="<?= $send_date; ?>"/></td>
					</tr>
					<tr>
						<td>
							<div><?= _l("Featured Designer / Product:"); ?></div>
							<div>
								<?= $this->builder->setConfig('manufacturer_id', 'name'); ?>
								<?= $this->builder->build('select', $data_designers, "newsletter[featured][designer][designer_id]", !empty($newsletter) ? $newsletter['featured']['designer']['designer_id'] : '', array('id' => 'designer_select')); ?>
							</div>
							<div>
								<?= $this->builder->setConfig('product_id', 'name'); ?>
								<?= $this->builder->build('select', $data_designer_products, "newsletter[featured][product][product_id]", !empty($newsletter) ? $newsletter['featured']['product']['product_id'] : '', array('id' => 'product_select')); ?>
							</div>
						</td>
						<td>
							<div id="newsletter_featured">
								<div class="product_image">
									<div>
										<?= $this->builder->imageInput("newsletter[featured][product][image]", !empty($newsletter) ? $newsletter['featured']['product']['image'] : ''); ?>
									</div>
									<div class="image_heading">
										<input type="text" name="newsletter[featured][product][name]" value="<?= !empty($newsletter) ? $newsletter['featured']['product']['name'] : ''; ?>"/>
									</div>
									<div>
										<input type="text" size="3" name="newsletter[featured][product][width]" value="<?= !empty($newsletter) ? $newsletter['featured']['product']['width'] : ''; ?>"/>
										x
										<input type="text" size="3" name="newsletter[featured][product][height]" value="<?= !empty($newsletter) ? $newsletter['featured']['product']['height'] : ''; ?>"/>
									</div>
									<div style="margin-top:10px"><?= _l("Product Main Image"); ?></div>
								</div>
								<div class="designer_image">
									<div>
										<?= $this->builder->imageInput("newsletter[featured][designer][image]", !empty($newsletter) ? $newsletter['featured']['designer']['image'] : ''); ?>
									</div>
									<div class="image_heading">
										<input type="text" name="newsletter[featured][designer][name]" value="<?= !empty($newsletter) ? $newsletter['featured']['designer']['name'] : ''; ?>"/>
									</div>
									<div>
										<input type="text" size="3" name="newsletter[featured][designer][width]" value="<?= !empty($newsletter) ? $newsletter['featured']['designer']['width'] : ''; ?>"/>
										x
										<input type="text" size="3" name="newsletter[featured][designer][height]" value="<?= !empty($newsletter) ? $newsletter['featured']['designer']['height'] : ''; ?>"/>
									</div>
									<div style="margin-top:10px"><?= _l("Designer Main Image"); ?></div>
								</div>
								<div class="featured_info">
									<div>
										<label for="designer_title"><?= _l("Title:"); ?></label>
										<input type="text" name="newsletter[featured][designer][title]" value="<?= !empty($newsletter) ? $newsletter['featured']['designer']['title'] : ''; ?>"/>
									</div>
									<div>
										<label for="designer_description"><?= _l("Description:"); ?></label>
										<textarea name="newsletter[featured][designer][description]"
											class="ckedit"><?= !empty($newsletter) ? $newsletter['featured']['designer']['description'] : ''; ?></textarea>
									</div>
									<div>
										<label for="designer_article"><?= _l("Article URL:"); ?></label>
										<input type="text" name="newsletter[featured][designer][article]" value="<?= !empty($newsletter) ? $newsletter['featured']['designer']['article'] : ''; ?>"/>
									</div>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<div><?= _l("Product List:<span class=\"help\">Drag and Drop the products to reorder them</span><br />"); ?></div>
							<div><input type="text" id="product_list_autocomplete"/></div>
							<div><span class="help">(<?= _l("autocomplete"); ?>)</span></div>
						</td>
						<td>
							<ol id="product_list" class="scrollbox editable_list">
								<? if (!empty($newsletter['products'])) { ?>
									<? foreach ($newsletter['products'] as $product) { ?>
										<li>
											<div class="editable_label">
												<input type="hidden" class="ac_item_id" name="newsletter[products][<?= $product['product_id']; ?>][product_id]" value="<?= $product['product_id']; ?>"/>
												<input type="text" size="60" name="newsletter[products][<?= $product['product_id']; ?>][name]" value="<?= $product['name']; ?>"/>
											</div>
											<img onclick="$(this).parent().remove()" src="<?= URL_THEME_IMAGE . 'delete.png'; ?>"/>
										</li>
									<? } ?>
								<? } ?>
							</ol>
						</td>
					</tr>
					<tr>
						<td>
							<div><?= _l("Designer List:<span class=\"help\">Drag and Drop the designers to reorder them</span><br />"); ?></div>
							<div><input type="text" id="designer_list_autocomplete"/></div>
							<div><span class="help">(<?= _l("autocomplete"); ?>)</span></div>
						</td>
						<td>
							<ol id="designer_list" class="scrollbox editable_list">
								<? if (!empty($newsletter['designers'])) { ?>
									<? foreach ($newsletter['designers'] as $designer) { ?>
										<li>
											<input type="hidden" class="ac_item_id" name="newsletter[designers][<?= $designer['designer_id']; ?>][designer_id]" value="<?= $designer['designer_id']; ?>"/>

											<div class="editable_label">
												<input type="text" name="newsletter[designers][<?= $designer['designer_id']; ?>][name]" value="<?= $designer['name']; ?>"/>
											</div>
											<img onclick="$(this).parent().remove()" src="<?= URL_THEME_IMAGE . 'delete.png'; ?>"/>
										</li>
									<? } ?>
								<? } ?>
							</ol>
						</td>
					</tr>
					<tr>
						<td><?= _l("B\'s Hot List:"); ?></td>
						<td>
							<div>
								<div><?= _l("Hot List Image:"); ?></div>
								<?= $this->builder->imageInput("newsletter[articles_image]", !empty($newsletter['articles_image']) ? $newsletter['articles_image'] : ''); ?>
								<span><?= _l("Article URL:"); ?></span>
								<input type="text" name="newsletter[articles_url]" value="<?= !empty($newsletter['articles_url']) ? $newsletter['articles_url'] : ''; ?>"
									size="50"/>
							</div>
							<div style="margin-top:10px;"><?= _l("Top 10 Articles:"); ?></div>
							<div id="add_article_form">
								<label for="add_article_title"><?= _l("Title:"); ?></label><input type="text"
									id="add_article_title"
									size="30"/>
								<label for="add_article_href"><?= _l("Article URL:"); ?></label><input type="text"
									id="add_article_href"
									size="80"/>
								<input type="button" value="Add Article" class="button" id="add_article_button"/>
							</div>
							<div>
								<ol id="article_list" class="scrollbox editable_list">
									<? $article_row = 1; ?>
									<? if (!empty($newsletter['articles'])) { ?>
										<? foreach ($newsletter['articles'] as $article) { ?>
											<li>
												<div class="editable_label">
													<input type="text" name="newsletter[articles][<?= $article_row ?>][title]" value="<?= $article['title']; ?>" size="30"/>
													<input type="text" name="newsletter[articles][<?= $article_row; ?>][href]" value="<?= $article['href']; ?>" size="50"/>
												</div>
												<img onclick="$(this).parent().remove()"
													src="<?= URL_THEME_IMAGE . 'delete.png'; ?>"/>
											</li>
											<? $article_row++; ?>
										<? } ?>
									<? } ?>
								</ol>
							</div>
						</td>
					</tr>
					<tr>
						<td><?= _l("On BettyConfidential Today:"); ?></td>
						<td>
							<div id="add_featured_article_form">
								<?= $this->builder->imageInput("", ''); ?>
								<label for="add_featured_article_title"><?= _l("Title:"); ?></label><input type="text"
									id="add_featured_article_title"
									size="30"/>
								<label for="add_featured_article_teaser"><?= _l("Teaser:"); ?></label><input type="text"
									id="add_featured_article_teaser"
									size="30"/>
								<label for="add_featured_article_href"><?= _l("Article URL:"); ?></label><input type="text"
									id="add_featured_article_href"
									size="80"/>
								<input type="button" value="Add Featured Article" class="button" id="add_featured_article_button"/>
							</div>
							<div>
								<ol id="featured_article_list" class="scrollbox large editable_list">
									<? $featured_article_row = 1; ?>
									<? if (!empty($newsletter['featured']['articles'])) { ?>
										<? foreach ($newsletter['featured']['articles'] as $article) { ?>
											<li>
												<div class="editable_label">
													<?= $this->builder->imageInput("newsletter[featured][articles][$featured_article_row][image]", $article['image']); ?>
													<input type="text" name="newsletter[featured][articles][<?= $featured_article_row ?>][title]" value="<?= $article['title']; ?>" size="30"/>
													<input type="text" name="newsletter[featured][articles][<?= $featured_article_row ?>][teaser]" value="<?= $article['teaser']; ?>" size="30"/>
													<input type="text" name="newsletter[featured][articles][<?= $featured_article_row; ?>][href]" value="<?= $article['href']; ?>" size="80"/>
												</div>
												<img onclick="$(this).parent().remove()"
													src="<?= URL_THEME_IMAGE . 'delete.png'; ?>"/>
											</li>
											<? $featured_article_row++; ?>
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

	<script type="text/javascript">

		designer_info = <?= json_encode($data_designers); ?>;

		$('#designer_select').change(function (event, first_time) {
			name_field = $('[name="newsletter[featured][designer][name]"]');
			image_field = $('[name="newsletter[featured][designer][image]"]');
			thumb = image_field.siblings('img');

			if (!$(this).val()) {
				name_field.val('');
				image_field.val('');
				image_field.siblings('a:last').click();
				return;
			}

			filter = {filter_manufacturer_id: $(this).val()};

			if (!first_time) {
				name_field.val($(this).find('option:selected').html());
			}

			for (i = 0; i < designer_info.length; i++) {
				if (designer_info[i].manufacturer_id == $(this).val()) {
					data = designer_info[i];
					break;
				}
			}

			if (typeof addSingleImage == 'function' && !first_time)
				addSingleImage(data['image'], image_field.attr('id'), thumb.attr('id'));

			$.post("<?= $url_product_select; ?>", {filter: filter, select: $('#product_select').val(), fields: 'image'},
				function (json) {
					$('#product_select').html(json['html']).data(json['option_data']);
					if (!first_time) {
						$('#product_select').change();
					}
				}, 'json');
		}).trigger('change', true);

		$('#product_select').change(function () {
			name_field = $('[name="newsletter[featured][product][name]"]');
			image_field = $('[name="newsletter[featured][product][image]"]');
			thumb = image_field.siblings('img');

			if (!$(this).val()) {
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

		$(document).ready(function () {
			$('#article_list').sortable({revert: true});
		});

		var article_row = <?= $article_row; ?>;
		$('#add_article_button').click(function () {
			if (!$('#add_article_title').val() || !$('#add_article_href').val()) return;

			html = '<li>';
			html += '	<div class="editable_label">';
			html += '			<input type="text" name="newsletter[articles][%row%][title]" value="%title%" />';
			html += '			<input type="text" name="newsletter[articles][%row%][href]" value="%href%" />';
			html += '	</div>';
			html += '	<img onclick="$(this).parent().remove()" src="<?= URL_THEME_IMAGE . 'delete.png'; ?>" />';
			html += '</li>';

			html = html.replace(/%row%/g, 'new' + article_row)
				.replace(/%title%/g, $('#add_article_title').val())
				.replace(/%href%/g, $('#add_article_href').val());

			$('#article_list').append(html);

			$('#add_article_form input[type=text]').val('');

			article_row++;
		});
	</script>
	<script type="text/javascript">
		$(document).ready(function () {
			$('#featured_article_list').sortable({revert: true});
		});
		var featured_article_row = <?= $featured_article_row; ?>;
		$('#add_featured_article_button').click(function () {
			if (!$('#add_featured_article_title').val() || !$('#add_featured_article_href').val()) return;

			html = '<li>';
			html += '	<div class="editable_label">';
			html += "			<?= $this->builder->imageInput("newsletter[featured][articles][%row%][image]", '%image%', null, null, null, null, true); ?>";
			html += '			<input type="text" name="newsletter[featured][articles][%row%][title]" value="%title%" />';
			html += '			<input type="text" name="newsletter[featured][articles][%row%][teaser]" value="%teaser%" />';
			html += '			<input type="text" name="newsletter[featured][articles][%row%][href]" value="%href%" size="80"/>';
			html += '	</div>';
			html += '	<img onclick="$(this).parent().remove()" src="<?= URL_THEME_IMAGE . 'delete.png'; ?>" />';
			html += '</li>';

			html = html.replace(/%row%/g, 'new' + featured_article_row)
				.replace(/%image%/g, $('#add_featured_article_form .image input').val())
				.replace(/%title%/g, $('#add_featured_article_title').val())
				.replace(/%teaser%/g, $('#add_featured_article_teaser').val())
				.replace(/%href%/g, $('#add_featured_article_href').val());

			$('#featured_article_list').append(html);

			$('#featured_article_list').children().last().find('.image img').attr('src', $('#add_featured_article_form .image img').attr('src'));

			$('#add_featured_article_form input[type=text]').val('');
			$('#add_featured_article_form .image a + a').click();

			featured_article_row++;
		});
	</script>

	<? $product_autocomplete_data = array(
		'selector' => '#product_list_autocomplete',
		'route'    => 'catalog/product/autocomplete',
		'filter'   => 'name',
		'label'    => 'name',
		'value'    => 'product_id',
		'callback' => 'callback_product_autocomplete',
	); ?>

	<?= $this->builder->js('autocomplete', $product_autocomplete_data); ?>

	<? $manufacturer_autocomplete_data = array(
		'selector' => '#designer_list_autocomplete',
		'route'    => 'catalog/manufacturer/autocomplete',
		'filter'   => 'name',
		'label'    => 'name',
		'value'    => 'manufacturer_id',
		'callback' => 'callback_designer_autocomplete',
	); ?>

	<?= $this->builder->js('autocomplete', $manufacturer_autocomplete_data); ?>

	<script type="text/javascript">
		$(document).ready(function () {
			$('#product_list, #designer_list').sortable({revert: true});
		});

		function callback_product_autocomplete(selector, data) {
			if ($('#product_list').find('.ac_item_id[value=" + data.product_id + "]').length > 0) return;

			html = '<li>';
			html += '	<input type="hidden" class="ac_item_id" name="newsletter[products][%product_id%][product_id]" value="%product_id%" />';
			html += '	<div class="autocomplete_label">';
			html += '			<input type="text" name="newsletter[products][%product_id%][name]" value="%name%" />';
			html += '	</div>';
			html += '	<img onclick="$(this).parent().remove()" src="<?= URL_THEME_IMAGE . 'delete.png'; ?>" />';
			html += '</li>';

			html = html.replace(/%product_id%/g, data.product_id)
				.replace(/%name%/g, data.name);

			$('#product_list').append(html);
		}

		function callback_designer_autocomplete(selector, data) {
			if ($('#designer_list').find('.ac_item_id[value=" + data.manufacturer_id + "]').length > 0) return;

			html = '<li>';
			html += '	<input type="hidden" class="ac_item_id" name="newsletter[designers][%manufacturer_id%][designer_id]" value="%manufacturer_id%" />';
			html += '	<div class="autocomplete_label">';
			html += '			<input type="text" name="newsletter[designers][%manufacturer_id%][name]" value="%name%" />';
			html += '	</div>';
			html += '	<img onclick="$(this).parent().remove()" src="<?= URL_THEME_IMAGE . 'delete.png'; ?>" />';
			html += '</li>';

			html = html.replace(/%manufacturer_id%/g, data.manufacturer_id)
				.replace(/%name%/g, data.name);

			$('#designer_list').append(html);
		}
	</script>

	<div id="preview_page">
		<div id="preview_window">
		</div>
	</div>

	<script type="text/javascript">//!<--
		function prepare_preview() {
			$('<span id="preview_shade"></span>').appendTo('body #container');

			shade = $('#preview_shade');
			shade.height(shade.parent().height());
			shade.width(shade.parent().width());

			$('#preview_page #preview_window').html('<div style="margin-top:20%;text-align:center">Loading Preview</div><div style="text-align:center"><img src="/admin/<?= URL_THEME_IMAGE . 'loading_bar.gif'; ?>" /></div>');
			$('#preview_page').fadeIn(500);
		}

		function handle_preview(html, textStatus) {
			content = $(html).get(1);

			$('#preview_window').fadeIn(500).html(html);

			shade.click(function () {
				$('#preview_window').html('');
				$('#preview_page').hide();
				$(this).remove();
			});

		}
	</script>

	<?= $this->builder->js('ckeditor'); ?>

	<?= $this->builder->js('datepicker'); ?>

	<?= $this->builder->js('errors'); ?>

	<?= $common_footer; ?>
