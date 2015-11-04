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
				<div class="row page-info">
					<div class="col xs-12 md-6 left top padding-right page-info-main">
						<div class="form-item page-title">
							<label for="title" class="col xs-3 md-2 left">{{Title}}</label>

							<div class="col xs-9 md-10 left value">
								<input id="title" type="text" name="title" size="60" value="<?= $title; ?>"/>
							</div>
						</div>

						<div class="form-item author-id">
							<label class="col xs-3 md-2 left">{{Author}}</label>

							<div class="col xs-9 md-10 left value">
								<?=
								build(array(
									'type'   => 'select',
									'name'   => 'author_id',
									'data'   => $data_authors,
									'label'  => 'username',
									'value'  => 'user_id',
									'select' => $author_id,
								)); ?>
							</div>
						</div>

						<div class="form-item status">
							<label class="col xs-3 md-2 left">{{Category}}</label>

							<div class="col xs-9 md-10 left value">
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
						</div>

						<div class="form-item status">
							<label class="col xs-3 md-2 left">{{Status}}</label>

							<div class="col xs-9 md-10 left value">
								<?=
								build(array(
									'type'   => 'select',
									'name'   => 'status',
									'data'   => App_Model_Page::$statuses,
									'select' => $status,
								)); ?>
							</div>
						</div>

						<div class="form-item date-published">
							<label class="col xs-3 md-2 left">{{Publish Date}}</label>

							<div class="col xs-9 md-10 left value">
								<input type="text" class="datetimepicker" name="date_published" value="<?= $date_published; ?>"/>
							</div>
						</div>

						<div class="form-item excerpt">
							<label class="col xs-3 md-2 left">{{Excerpt}}</label>

							<div class="col xs-9 md-10 left value">
								<textarea name="excerpt" cols="60" rows="5"><?= $excerpt; ?></textarea>
							</div>
						</div>
					</div>

					<div class="col xs-12 md-6 left top page-info-secondary">
						<div class="form-item main-image">
							<label class="col xs-3 md-2 left">{{Main Image}}</label>

							<div class="col xs-9 md-10 left value">
								<input type="text" class="image-input" name="meta[image]" value="<?= $meta['image']; ?>" data-thumb="<?= $meta['image'] ? image($meta['image'], null, option('admin_thumb_height')) : ''; ?>"/>

								<div class="image-size">
									<input type="text" size="3" placeholder="{{auto}}" name="meta[image_width]" value="<?= $meta['image_width']; ?>"/>
									x
									<input type="text" size="3" placeholder="{{auto}}" name="meta[image_height]" value="<?= $meta['image_height']; ?>"/>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="row page-editor">
					<div class="editor-col col xs-12 md-6 top left padding-right">
						<div class="html-content form-item code-editor">
							<label>{{HTML}}</label>

							<div class="value">
								<textarea id="html-editor" name="content"><?= $content; ?></textarea>
							</div>
						</div>

						<div class="style-content form-item code-editor">
							<label>{{Style}}</label>

							<div class="value">
								<textarea id="style-editor" name="style"><?= $style; ?></textarea>
							</div>
						</div>
					</div>

					<div class="preview-col col xs-12 md-6 top">
						<div class="row page-controls left">
							<div class="col xs-6 left">
								<a class="refresh-preview button" data-loading="{{Refreshing...}}">{{Refresh}}</a>
							</div>
						</div>

						<div class="row page-preview-iframe">
							<iframe id="preview-frame" name="preview-frame" frameborder="1" scrolling="auto" marginheight="0" accept-charset="UTF-8"></iframe>
						</div>
					</div>
				</div>
			</div>
			<!-- /tab-content -->

			<div id="tab-data">
				<div class="form-item excerpt">
					<label class="col xs-3 md-2 left">
						{{URL Alias:}}
						<span class="help">{{The Search Engine Optimized URL.}}</span>
					</label>

					<div class="col xs-9 md-10 left value">
						<input type="text" name="alias" size="60" value="<?= $alias; ?>"/>
					</div>
				</div>

				<div class="form-item excerpt">
					<label class="col xs-3 md-2 left">{{Meta Keywords:}}</label>

					<div class="col xs-9 md-10 left value">
						<textarea name="meta_keywords" rows="4" cols="60"><?= $meta_keywords; ?></textarea>
					</div>
				</div>


				<div class="form-item excerpt">
					<label class="col xs-3 md-2 left">{{Meta Description:}}</label>

					<div class="col xs-9 md-10 left value">
						<textarea name="meta_description" rows="8" cols="60"><?= $meta_description; ?></textarea>
					</div>
				</div>

				<div class="form-item cache">
					<label class="col xs-3 md-2 left">
						{{Cache:}}
						<span class="help">{{This will allow the fully rendered page to be cached, greatly increasing performance.}}
							<BR/><BR/>
							{{NOTE: While any changes to the page will be immediately visible, dynamic data on this page will not be updated until the page cache is cleared (you can save changes to the page to clear the cache).}}
						</span>
					</label>

					<div class="col xs-9 md-10 left value">
						<?= build(array(
							'type'   => 'radio',
							'name'   => 'cache',
							'data'   => array(
								1 => '{{Yes}}',
								0 => '{{No}}',
							),
							'select' => $cache,
							'#class' => 'panel',
						)); ?>
					</div>
				</div>
			</div>
			<!-- /tab-data -->

			<div id="tab-design">
				<div class="form-item show-title">
					<label for="show-title" class="col xs-3 md-2 left">{{Display Title?}}</label>

					<div class="col xs-9 md-10 left value">
						<?= build(array(
							'type'   => 'radio',
							'name'   => 'meta[show_title]',
							'data'   => array(
								1 => '{{Yes}}',
								0 => '{{No}}',
							),
							'select' => $meta['show_title'],
							'#class' => 'panel',
						)); ?>
					</div>
				</div>

				<div class="form-item show-breadcrumbs">
					<label for="show-breadcrumbs" class="col xs-3 md-2 left">{{Show Breadcrumbs?}}</label>

					<div class="col xs-9 md-10 left value">
						<?= build(array(
							'type'   => 'radio',
							'name'   => 'meta[show_breadcrumbs]',
							'data'   => array(
								1 => '{{Yes}}',
								0 => '{{No}}',
							),
							'select' => $meta['show_breadcrumbs'],
							'#class' => 'panel',
						)); ?>
					</div>
				</div>

				<div class="form-item template">
					<label for="template" class="col xs-3 md-2 left">{{Template}}</label>

					<div class="col xs-9 md-10 left value">
						<?=
						build(array(
							'type'   => 'select',
							'name'   => 'template',
							'data'   => $data_templates,
							'select' => $template,
							'#id'    => 'template',
						)); ?>
					</div>
				</div>

				<div class="form-item layout-id">
					<label for="layout-id" class="col xs-3 md-2 left">{{Layout}}</label>

					<div class="col xs-9 md-10 left value">
						<?= build(array(
							'type'   => 'select',
							'name'   => 'layout_id',
							'data'   => $data_layouts,
							'select' => $layout_id,
							'value'  => 'layout_id',
							'label'  => 'name',
							'#id'    => 'layout-id',
						)); ?>

						<a id="create-layout" class="link_button">{{[ Create Layout for this page ]}}</a>
					</div>
				</div>
			</div>
			<!-- /tab-design -->
		</div>
	</form>
</section>


<script type="text/javascript">
	$('#html-editor').codemirror({mode: 'html', onChange: refresh_delay, change: refresh_delay});
	$('#style-editor').codemirror({mode: 'css', change: refresh_delay});

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

		return true;
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

	$('#create-layout').click(function () {
		layout_name = $('[name=title]').val();

		if (!layout_name) {
			$.ampAlert('{{You must specify a name before you can create a new layout!}}');
			return false;
		}

		data = {
			name: layout_name
		};

		$('#layout-select').load("<?= site_url('admin/page/create-layout'); ?>", data, function () {
		});

		return false;
	});

	$.ac_datepicker();

	$('#tabs a').tabs();

	$('.image-input').ac_imageinput({
		width: 'auto'
	});
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
