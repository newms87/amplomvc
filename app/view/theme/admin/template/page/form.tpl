<?= $is_ajax ? '' : call('admin/header'); ?>
<section class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form id="page-form" action="<?= site_url($model['path'] . '/save', 'page_id=' . $page_id); ?>" method="post" enctype="multipart/form-data" class="box ctrl-save">
		<div class="row heading left">
			<h1>
				<img src="<?= theme_url('image/setting.png'); ?>" alt=""/> {{<?= $model['title']; ?>}}
				<div class="page-url">
					<span class="prefix-url"><?= site_url($type . '/'); ?></span>
					<input type="text" name="name" value="<?= $name; ?>"/>
				</div>

				<a class="page-view" href="<?= site_url($type . '/' . $name); ?>" target="_blank">{{View}}</a>
			</h1>

			<div class="buttons">
				<button>{{Save}}</button>

				<? if ($status < App_model_Page::STATUS_PUBLISHED) { ?>
					<button onclick="$('[name=status]').val(<?= App_Model_Page::STATUS_PUBLISHED; ?>)">{{Publish}}</button>
				<? } ?>

				<a href="<?= site_url($model['path']); ?>" class="button cancel">{{Cancel}}</a>
			</div>
		</div>

		<div class="row left section">

			<div id="tabs" class="htabs">
				<a href="#tab-content">{{Content}}</a>
				<a href="#tab-data">{{Data}}</a>
				<a href="#tab-design">{{Design}}</a>
			</div>

			<div id="tab-content">
				<div class="col xs-12 md-6 left page-info top">
					<div class="form-item page-title">
						<div class="title">{{Title}}</div>
						<input type="text" name="title" size="60" value="<?= $title; ?>"/>
					</div>

					<div class="form-item show-title">
						<label for="show-title">{{Display Title?}}</label>

						<?= build(array(
							'type'   => 'radio',
							'name'   => 'options[show_title]',
							'data'   => array(
								1 => '{{Yes}}',
								0 => '{{No}}',
							),
							'select' => $options['show_title'],
							'#class' => 'panel',
						)); ?>
					</div>

					<div class="form-item show-breadcrumbs">
						<label for="show-breadcrumbs">{{Show Breadcrumbs?}}</label>

						<?= build(array(
							'type'   => 'radio',
							'name'   => 'options[show_breadcrumbs]',
							'data'   => array(
								1 => '{{Yes}}',
								0 => '{{No}}',
							),
							'select' => $options['show_breadcrumbs'],
							'#class' => 'panel',
						)); ?>
					</div>

					<div class="form-item author-id">
						<label>{{Author}}</label>

						<?=
						build(array(
							'type'   => 'select',
							'name'   => 'status',
							'data'   => $data_authors,
							'label'  => 'username',
							'value'  => 'user_id',
							'select' => $author_id,
						)); ?>
					</div>

					<div class="form-item status">
						<label>{{Category}}</label>

						<?=
						build(array(
							'type'   => 'multiselect',
							'name'   => 'categories',
							'data'   => $data_categories,
							'select' => $categories,
							'label'  => 'title',
							'value'  => 'category_id',
							'#class' => 'amp-select',
						)); ?>
					</div>

					<div class="form-item status">
						<label>{{Status}}</label>

						<?=
						build(array(
							'type'   => 'select',
							'name'   => 'status',
							'data'   => App_Model_Page::$statuses,
							'select' => $status,
						)); ?>
					</div>

					<div class="form-item date-published">
						<label>{{Publish Date}}</label>

						<input type="text" class="datetimepicker" name="date_published" value="<?= $date_published; ?>"/>
					</div>

					<div class="html-content form-item code-editor">
						<div class="label">{{HTML}}</div>
						<textarea id="html-editor" name="content"><?= $content; ?></textarea>
					</div>

					<div class="style-content form-item code-editor">
						<div class="label">{{Style}}</div>
						<textarea id="style-editor" name="style"><?= $style; ?></textarea>
					</div>
				</div>

				<div class="col xs-12 md-6 page-secondary top">
					<div class="row page-controls left">
						<div class="col xs-6 left">
							<a class="refresh-preview button" data-loading="{{Refreshing...}}">{{Refresh}}</a>
						</div>
					</div>

					<div class="row page-preview-iframe">
						<iframe id="preview-frame" name="preview-frame" frameborder="1" scrolling="auto" marginheight="0"></iframe>
					</div>
				</div>

			</div>
			<!-- /tab-content -->

			<div id="tab-data">
				<table class="form">
					<tr>
						<td class="required">
							{{URL Alias:}}
							<span class="help">{{The Search Engine Optimized URL.}}</span>
						</td>
						<td>
							<input type="text" name="alias" size="60" value="<?= $alias; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{Meta Keywords:}}</td>
						<td>
							<textarea name="meta_keywords" rows="4" cols="60"><?= $meta_keywords; ?></textarea>
						</td>
					</tr>
					<tr>
						<td>{{Meta Description:}}</td>
						<td>
							<textarea name="meta_description" rows="8" cols="60"><?= $meta_description; ?></textarea>
						</td>
					</tr>
				</table>
			</div>
			<!-- /tab-data -->

			<div id="tab-design">
				<table class="form">
					<tr>
						<td class="required">{{Template}}</td>
						<td>
							<?=
							build(array(
								'type'   => 'select',
								'name'   => 'template',
								'data'   => $data_templates,
								'select' => $template,
							)); ?>
						</td>
					</tr>
					<tr>
						<td class="required">{{Theme}}</td>
						<td>
							<?=
							build(array(
								'type'   => 'select',
								'name'   => 'theme',
								'data'   => $data_themes,
								'select' => $theme,
								'value'  => 'name',
								'label'  => 'name',
							)); ?>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Layout:}}</td>
						<td>
							<div id="layout_select">
								<?=
								build(array(
									'type'   => 'select',
									'name'   => 'layout_id',
									'data'   => $data_layouts,
									'select' => $layout_id,
									'value'  => 'layout_id',
									'label'  => 'name',
								)); ?>
							</div>
							<a id="create_layout" class="link_button">{{[ Create Layout for this page ]}}</a>
							<span id="create_layout_load" style="display:none">{{Please wait...}}</span>
						</td>
					</tr>
				</table>
			</div>
			<!-- /tab-design -->

		</div>
	</form>
</section>


<script type="text/javascript">
	$('#html-editor').codemirror({mode: 'html', update: refresh_delay});
	$('#style-editor').codemirror({mode: 'css', update: refresh_delay});

	$('.refresh-preview').click(refresh_preview);
	$('[name]').change(refresh_preview);

	$('#preview-frame').load(function () {
		$('.refresh-preview').loading('stop')
	});

	function refresh_delay(e) {
		if (e) {
			refresh_delay.d = 2;
		}

		if (!refresh_delay.d) {
			refresh_delay.started = false;
			refresh_preview();
		} else if (!e || !refresh_delay.started) {
			refresh_delay.started = true;
			setTimeout(function () {
				refresh_delay.d--;
				refresh_delay()
			}, 1000);
		}
	}

	function refresh_preview() {
		var $form = $('#page-form');
		var action = $form.attr('action');

		$form.attr('action', '<?= site_url('page/preview'); ?>');
		$form.attr('target', 'preview-frame');

		$form.submit();

		$form.attr('action', action);
		$form.removeAttr('target');
	}

	refresh_preview();

	$('[name=name]').ampResize();

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

		$('#layout_select').load("<?= site_url('admin/page/create-layout'); ?>", data, function () {
			$('#create_layout_load').hide();
			$('#create_layout').show();
		});

		return false;
	});

	$.ac_datepicker();

	$('#tabs a').tabs();
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
