<?= $header; ?>
<div class="section">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= $head_title; ?></h1>

			<div class="buttons">
				<a onclick="$('#form').submit()" class="button"><?= $button_save; ?></a>
				<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
			</div>
		</div>
		<div class="section">

			<div id="tabs" class="htabs">
				<a href="#tab-general"><?= $tab_general; ?></a>
				<a href="#tab-content"><?= _("Content"); ?></a>
				<a href="#tab-design"><?= $tab_design; ?></a>
			</div>

			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_title; ?></td>
							<td>
								<input type="text" name="title" size="60" value="<?= $title; ?>"/>
								<div class="display_title">
									<input type="checkbox" id="display_title" name="display_title" <?= $display_title ? "checked=\"checked\"" : ''; ?> value="1" />
									<label for="display_title"><?= $entry_display_title; ?></label>
								</div>
							</td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_alias; ?></td>
							<td><input type="text" name="alias" size="60" value="<?= $alias; ?>"/></td>
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
							<td><?= $entry_status; ?></td>
							<td><?= $this->builder->build('select', $data_statuses, 'status', (int)$status); ?></td>
						</tr>
					</table>
				</div><!-- /tab-general -->

				<div id="tab-content">
					<div id="code_editor">
						<h2><?= $entry_content; ?></h2>
						<textarea id="html_editor" name="content"><?= $content; ?></textarea>
					</div>

					<div id="code_preview">
						<iframe id="preview_frame"></iframe>
					</div>

				</div><!-- /tab-content -->

				<div id="tab-design">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_layout; ?></td>
							<td>
								<? $this->builder->setConfig('layout_id', 'name'); ?>
								<div
									id="layout_select"><?= $this->builder->build('select', $data_layouts, "layout_id", $layout_id); ?></div>
								<a id="create_layout" class="link_button"><?= $button_create_layout; ?></a>
								<span id="create_layout_load" style="display:none"><?= $text_creating_layout; ?></span>
							</td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_store; ?></td>
							<? $this->builder->setConfig('store_id', 'name'); ?>
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
											<a id="add_block" href="<?= $url_blocks; ?>" target="_blank"
											   class="button"><?= $button_add_blocks; ?></a>
										</td>
									</tr>
									</tfoot>
								</table>
							</td>
						</tr>
					</table>
				</div><!-- /tab-design -->

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

		$('#assigned_block_list tbody').append($(template).attr('id', ''));
	}
	;

	function load_assigned_blocks() {
		$('#assigned_block_list tbody').empty();

		url = "<?= $url_load_blocks; ?>";

		data = $('[name="stores[]"], [name=layout_id]').serialize();
		$.post(url, data, function (json) {
			if (json) {
				for (var b in json) {
					add_block_item(json[b]['display_name'], json[b]['store_name'], json[b]['position']);
				}
			}
		}, 'json');
	}

	$('[name="stores[]"], [name=layout_id]').change(load_assigned_blocks).first().change();

	$('#create_layout').click(function () {
		url = "<?= $url_create_layout; ?>";

		layout_name = $('[name=title]').val();

		if (!layout_name) {
			alert('You must specify a name before you can create a new layout!');
			return false;
		}

		data = {
			name: layout_name
		};

		$('#create_layout_load').show();
		$("#create_layout").hide();

		$('#layout_select').load(url, data, function () {
			$('#create_layout_load').hide();
			$('#create_layout').show();
		});

		return false;
	});

	$('#html_editor').codemirror({mode: 'html'})

	$('#preview_frame').attr('src',"<?= $page_preview; ?>");

	$('#html_editor')[0].cm_editor.mirror.on('keyup',function(instance, changeObj){
		$('#preview_frame').contents().find('#content_holder .page_content').html(instance.getValue());
	});

	$(document).bind('keydown', function(e) {
		if(e.ctrlKey && (e.which == 83)) {
			$('#html_editor')[0].cm_editor.mirror.save();

			$('#form').postForm(function(response){
				handle_response(response);
			}, 'json');

			e.preventDefault();
			return false;
		}
	});

	$('#tabs a').tabs();
//--></script>

<?= $this->builder->js('translations', $translations); ?>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
