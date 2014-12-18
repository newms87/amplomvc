<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form action="<?= site_url('admin/settings/admin/save'); ?>" method="post" enctype="multipart/form-data" class="box ctrl-save">
		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/setting.png'); ?>" alt=""/>
				{{General Settings}}
			</h1>

			<div class="buttons">
				<button>{{Save}}</button>
				<a href="<?= site_url('admin/settings'); ?>" class="button cancel">{{Cancel}}</a>
			</div>
		</div>
		<div class="section">
			<div id="tabs" class="htabs">
				<a href="#tab-general">{{Settings}}</a>
				<a href="#tab-image">{{Image}}</a>
			</div>

			<div id="tab-general">
				<table class="form">
					<tr>
						<td>{{Administration Language:}}</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'admin_language',
								'data'   => $data_languages,
								'select' => $admin_language,
								'key'    => 'code',
								'value'  => 'name',
							)); ?>
						</td>
					</tr>
					<tr>
						<td>
							{{Administration Bar}}
							<span class="help">{{This will display a small toolbar on the site fronts when logged into the Admin Panel}}</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'radio',
								'name'   => 'admin_bar',
								'data'   => $data_yes_no,
								'select' => $admin_bar
							)); ?>
						</td>
					</tr>
					<tr>
						<td class="required">{{Show Breadcrumbs?}}
							<span class="help">{{Display breadcrumbs in the Admin Panel?}}</span>
						</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => "admin_show_breadcrumbs",
								'data'   => $data_yes_no,
								'select' => $admin_show_breadcrumbs
							)); ?>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Admin Breadcrumb Separator:}}</td>
						<td>
							<input type="text" style="font-size:20px" name="admin_breadcrumb_separator" value="<?= $admin_breadcrumb_separator; ?>" size="1"/>
						</td>
					</tr>
					<tr>
						<td class="required">{{Listing Record Limit (Default):}}
							<span class="help">{{Determines how many admin records are shown by default per page on listing pages}}</span>
						</td>
						<td>
							<input type="text" name="admin_list_limit" value="<?= $admin_list_limit; ?>" size="3"/>
						</td>
					</tr>
				</table>
			</div>

			<div id="tab-image">
				<table class="form">
					<tr>
						<td>{{Admin Logo:}}</td>
						<td>
							<input type="text" class="imageinput" name="admin_logo" data-thumb="<?= image($admin_logo, $admin_logo_width, $admin_logo_height); ?>" value="<?= $admin_logo; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required">{{Logo Size}}</td>
						<td>
							<div class="store-logo-size">
								<input type="text" name="admin_logo_width" value="<?= $admin_logo_width; ?>" size="3"/>
								x
								<input type="text" name="admin_logo_height" value="<?= $admin_logo_height; ?>" size="3"/>
							</div>
							<br/>
							<div class="store-logo-x">
								<label>{{Image srcset X}}</label>
								<?= build(array(
									'type'   => 'select',
									'name'   => 'admin_logo_srcset',
									'data'   => array(
										1 => '1x',
										2 => '2x',
										3 => '3x'
									),
									'select' => $admin_logo_srcset,
								)); ?>
								<span class="help">{{If greater than 1x, sets img srcset attribute and scales down from original image. (eg. if 3x, then 1x size set for src will be 1/3 of original image)}}</span>
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<span>{{Admin Icon:}}</span>
							<span class="help">{{Use a png file that is at least 152px X 152px. Then click generate to generate all required icon file sizes and the .ico file.}}</span>
						</td>
						<td>
							<div id="icon-generator">
								<div class="generate">
									<div class="icon-file">
										<input type="text" class="imageinput" name="admin_icon[orig]" value="<?= $admin_icon['orig']['src']; ?>" data-thumb="<?= $admin_icon['orig']['thumb']; ?>"/>

										<div class="icon-label">
											<a id="generate-icons" data-loading="{{Generating...}}" class="button">{{Generate Icon Files}}</a>
										</div>
									</div>
								</div>
								<div class="icon-files left">
									<div class="icon-file icon-ico">
										<input type="text" class="imageinput" name="admin_icon[ico]" value="<?= $admin_icon['ico']['src']; ?>" data-thumb="<?= $admin_icon['ico']['thumb']; ?>"/>

										<div class="icon-label">{{ICO File}}</div>
									</div>
									<? foreach ($data_icon_sizes as $size) { ?>
										<div class="icon-file icon-size">
											<? $key = $size . 'x' . $size; ?>
											<input type="text" class="imageinput" name="admin_icon[<?= $key; ?>]" value="<?= $admin_icon[$key]['src']; ?>" data-thumb="<?= $admin_icon[$key]['thumb']; ?>" data-width="<?= $size; ?>" data-height="<?= $size; ?>"/>

											<div class="icon-label"><?= _l("%s X %s Icon", $size, $size); ?></div>
										</div>
									<? } ?>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Image Thumbnail Size:}}</td>
						<td>
							<input type="text" name="admin_thumb_width" value="<?= $admin_thumb_width; ?>" size="3"/>
							x
							<input type="text" name="admin_thumb_height" value="<?= $admin_thumb_height; ?>" size="3"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Listing Image Size:}}</td>
						<td>
							<input type="text" name="admin_list_image_width" value="<?= $admin_list_image_width; ?>" size="3"/>
							x
							<input type="text" name="admin_list_image_height" value="<?= $admin_list_image_height; ?>" size="3"/>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</form>
</div>
</div>

<script type="text/javascript">
	$('[name=admin_logo]').ac_imageinput({width: '<?= $admin_logo_width; ?>', height: '<?= $admin_logo_height; ?>'});

	$('[name=admin_logo_width],[name=admin_logo_height]').change(function () {
		var w = $('[name=admin_logo_width]').val();
		var h = $('[name=admin_logo_height]').val();
		$('[name=admin_logo]').siblings('.thumb').css({
			width: (!w || w == '0') ? 'auto' : w,
			height: (!h || h == '0') ? 'auto' : h
		});
	});

	$('#generate-icons').click(function () {
		var $this = $(this);
		var icon = $('[name="admin_icon[orig]"]').val();

		if (!icon) {
			return $('#icon-generator').ac_msg('error', "{{You must choose an icon PNG image file first}}");
		}

		$this.loading();
		$.post("<?= site_url('admin/settings/general/generate-icons'); ?>", {icon: icon}, function (json) {
			$this.loading('stop');

			var $gen = $('#icon-generator');

			for (var c in json) {
				input = $gen.find('[name="admin_icon[' + c + ']"]').val(json[c].relpath);
				input.closest('.icon-file').find('img.thumb').attr('src', json[c].url);
			}
		}, 'json');
	});

	$('.icon-file .imageinput').ac_imageinput({width: 'auto', height: 'auto'});

	$('.imageinput').ac_imageinput();

	$('#tabs a').tabs();

	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
