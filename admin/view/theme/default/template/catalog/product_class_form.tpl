<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'product.png'; ?>" alt=""/> <?= _l("Product Classes"); ?></h1>

				<div class="buttons">
					<a onclick="$('#form').submit();" class="button"><?= _l("Save"); ?></a>
					<a href="<?= $cancel; ?>" class="button"><?= _l("Cancel"); ?></a>
				</div>
			</div>
			<div class="section">
				<form action="<?= $save; ?>" method="post" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= _l("Product Class Name:"); ?></td>
							<td><input type="text" name="name" size="60" value="<?= $name; ?>"/></td>
						</tr>
						<tr>
							<td><?= _l("Front End Template: <span class=\"help\">This is the template used when customers view the product page</span>"); ?></td>
							<td>
								<table class="list">
									<thead>
									<tr>
										<td><?= _l("Theme Name:"); ?></td>
										<td><?= _l("Theme Template:"); ?></td>
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
							<td><?= _l("Admin Template: <span class=\"help\">This is the template for the product input form in the Admin Panel</span>"); ?></td>
							<td>
								<table class="list">
									<thead>
									<tr>
										<td><?= _l("Theme Name:"); ?></td>
										<td><?= _l("Theme Template:"); ?></td>
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
