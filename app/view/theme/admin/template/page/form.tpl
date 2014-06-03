<?= call('admin/common/header'); ?>
<section class="section">
	<?= breadcrumbs(); ?>

	<form id="page-form" action="<?= $save; ?>" method="post" enctype="multipart/form-data" class="box">
		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= _l("Page"); ?>
				<div class="page-url">
					<span class="prefix-url"><?= site_url('page/'); ?></span>
					<input type="text" name="name" value="<?= $name;?>" />
				</div>

				<a class="page-view" href="<?= site_url('page/' . $name); ?>" target="_blank">View</a>
			</h1>

			<div class="buttons">
				<button><?= _l("Save"); ?></button>
				<a href="<?= site_url('admin/page'); ?>" class="button"><?= _l("Cancel"); ?></a>
			</div>
		</div>
		<div class="section clearfix">

			<div id="tabs" class="htabs">
				<a href="#tab-content"><?= _l("Content"); ?></a>
				<a href="#tab-data"><?= _l("Data"); ?></a>
				<a href="#tab-design"><?= _l("Design"); ?></a>
			</div>

			<div id="tab-content">
				<div id="code_editor_preview">
					<div class="page_title">
						<div class="title"><?= _l("Page Title"); ?></div>
						<input type="text" name="title" size="60" value="<?= $title; ?>"/>
						<span class="display_title">
							<input type="checkbox" id="display_title" name="display_title" <?= $display_title ? "checked=\"checked\"" : ''; ?> value="1"/>
							<label for="display_title"><?= _l("Display Title?"); ?></label>
						</span>
					</div>

					<div class="html_title"><?= _l("HTML"); ?></div>
					<textarea id="html_editor" name="content"><?= $content ? file_get_contents($content) : ''; ?></textarea>

					<div class="css_title"><?= _l("Style"); ?></div>
					<textarea id="css_editor" name="style"><?= $style ? file_get_contents($style) : $style; ?></textarea>
				</div>

				<div id="code_preview">
					<div id="zoom_preview">
						<input type="text" id="zoom_value" value="80%"/>

						<div class="zoom_change">
							<img class="zoom_in" src="<?= theme_url('image/zoom-in.png') ?>"/>
							<img class="zoom_out" src="<?= theme_url('image/zoom-out.png'); ?>"/>
						</div>
					</div>
					<iframe id="preview-frame" frameborder="1" scrolling="auto" marginheight="0"></iframe>
				</div>

			</div>
			<!-- /tab-content -->

			<div id="tab-data">
				<table class="form">
					<tr>
						<td class="required">
							<?= _l("URL Alias:"); ?>
							<span class="help"><?= _l("The Search Engine Optimized URL."); ?></span>
						</td>
						<td>
							<input type="text" name="alias" size="60" value="<?= $alias; ?>"/>
						</td>
					</tr>
					<tr>
						<td><?= _l("Meta Keywords:"); ?></td>
						<td>
							<textarea name="meta_keywords" rows="4" cols="60"><?= $meta_keywords; ?></textarea>
						</td>
					</tr>
					<tr>
						<td><?= _l("Meta Description:"); ?></td>
						<td>
							<textarea name="meta_description" rows="8" cols="60"><?= $meta_description; ?></textarea>
						</td>
					</tr>
					<tr>
						<td><?= _l("Status:"); ?></td>
						<td><?=
							build('select', array(
								'name'   => 'status',
								'data'   => $data_statuses,
								'select' => $status
							)); ?></td>
					</tr>
				</table>
			</div>
			<!-- /tab-data -->

			<div id="tab-design">
				<table class="form">
					<tr>
						<td class="required"> <?= _l("Layout:"); ?></td>
						<td>
							<div id="layout_select">
								<?=
								build('select', array(
									'name'   => 'layout_id',
									'data'   => $data_layouts,
									'select' => $layout_id,
									'key'    => 'layout_id',
									'value'  => 'name',
								)); ?>
							</div>
							<a id="create_layout" class="link_button"><?= _l("[ Create Layout for this page ]"); ?></a>
							<span id="create_layout_load" style="display:none"><?= _l("Please wait..."); ?></span>
						</td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Stores:"); ?></td>
						<td>
							<?=
							build('multiselect', array(
								'name'   => "stores",
								'data'   => $data_stores,
								'select' => $stores,
								'key'    => 'store_id',
								'value'  => 'name',
							)); ?>
						</td>
					</tr>
				</table>
			</div>
			<!-- /tab-design -->

		</div>
	</form>
</section>


<script type="text/javascript">
	var $preview;

	$('#preview-frame').load(function() {
		$preview = $('#preview-frame').contents();
		//$preview.find('#container').draggable();

		update_zoom();
	});

	$('#create_layout').click(function () {
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

		$('#layout_select').load("<?= $url_create_layout; ?>", data, function () {
			$('#create_layout_load').hide();
			$('#create_layout').show();
		});

		return false;
	});

	function get_zoom_value() {
		return (parseInt($('#zoom_value').val()) || 0) / 100;
	}

	function update_zoom() {
		var z = get_zoom_value();
		var new_css = {
			'-webkit-transform': 'scale3d(' + z + ',' + z + ',1)',
			'transform': 'scale3d(' + z + ',' + z + ',1)'
		};
		$preview.find('#container').css(new_css);
	}

	$('#zoom_value').keyup(update_zoom);

	$('#zoom_preview .zoom_in, #zoom_preview .zoom_out').click(function () {
		var z = get_zoom_value();
		var zoom = $(this).hasClass('zoom_out') ? Math.max(z - .1, .1) : Math.min(z + .1, 3);
		$('#zoom_value').val(parseInt(zoom * 100) + '%');
		update_zoom();
	});

	$('#html_editor').codemirror({mode: 'html'});
	$('#css_editor').codemirror({mode: 'css'});

	$('#html_editor')[0].cm_editor.mirror.on('keyup', function (instance, changeObj) {
		$preview.find('.page-content .wrap').html(instance.getValue());
	});

	$('#css_editor')[0].cm_editor.mirror.on('keyup', function (instance, changeObj) {
		$preview.find('#page-style').html(instance.getValue());
	});

	$('[name="title"]').keyup(function () {
		console.log($(this).val());
		$preview.find('#page-title').html($(this).val());
	});

	$('[name=display_title]').change(function () {
		$preview.find('#page-title').stop().toggle($(this).val());
	});

	$(document).bind('keydown', function (e) {
		if (e.ctrlKey && (e.which == 83)) {
			var $form = $('#page-form');
			$('#html_editor')[0].cm_editor.mirror.save();
			$('#css_editor')[0].cm_editor.mirror.save();

			$.post($form.attr('action'), $form.serialize(), function (response) {
				$form.ac_msg(response);
			}, 'json');

			e.preventDefault();
			return false;
		}
	});

	$('#tabs a').tabs();

	$(document).ready(function () {
		$('#preview-frame').attr('src', "<?= $page_preview; ?>");
	});

	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= call('admin/common/footer'); ?>
