<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'product.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
					<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
				</div>
			</div>
			<div class="section">
				<form action="<?= $save; ?>" method="post" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_name; ?></td>
							<td><input type="text" name="name" size="60" value="<?= $name; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_front_template; ?></td>
							<td>
								<table class="list">
									<thead>
									<tr>
										<td><?= $entry_theme_name; ?></td>
										<td><?= $entry_theme_template; ?></td>
										<td><?= _l("Controller:"); ?></td>
									</tr>
									</thead>
									<tbody>
									<? foreach ($data_front_templates as $theme => $templates) { ?>
										<tr>
											<td><?= $theme; ?></td>
											<td><?= $this->builder->build('select', $templates, "front_template[$theme]", isset($front_template[$theme]) ? $front_template[$theme] : ''); ?></td>
											<td><input type="text" name="front_controller[<?= $theme; ?>]" value="<?= $front_controller[$theme]; ?>" /></td>
										</tr>
									<? } ?>
									</tbody>
								</table>
							</td>
						</tr>
						<tr>
							<td><?= $entry_admin_template; ?></td>
							<td>
								<table class="list">
									<thead>
									<tr>
										<td><?= $entry_theme_name; ?></td>
										<td><?= $entry_theme_template; ?></td>
										<td><?= _l("Controller:"); ?></td>
									</tr>
									</thead>
									<tbody>
									<? foreach ($data_admin_templates as $theme => $templates) { ?>
										<tr>
											<td><?= $theme; ?></td>
											<td><?= $this->builder->build('select', $templates, "admin_template[$theme]", isset($admin_template[$theme]) ? $admin_template[$theme] : ''); ?></td>
											<td><input type="text" name="admin_controller[<?= $theme; ?>]" value="<?= $admin_controller[$theme]; ?>" /></td>
										</tr>
									<? } ?>
									</tbody>
								</table>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
