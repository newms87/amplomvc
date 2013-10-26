<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a>
					<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
				</div>
			</div>
			<div class="section">
				<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td valign="top"><a id="add_override" class="button"><?= $button_add; ?></a></td>
							<td>
								<ul id="controller_override_list" class="easy_list">
									<? foreach ($controller_overrides as $row => $override) { ?>
										<li class="controller_override" data-row="<?= $row; ?>">
											<input class="original" size="50" type="text" name="controller_overrides[<?= $row; ?>][original]" value="<?= $override['original']; ?>"/>
											<input class="alternate" size="50" type="text" name="controller_overrides[<?= $row; ?>][alternate]" value="<?= $override['alternate']; ?>"/>
											<input class="condition" size="50" type="text" name="controller_overrides[<?= $row; ?>][condition]" value="<?= $override['condition']; ?>"/>
											<a class="delete button text" onclick="$(this).closest('li').remove()"><?= $button_delete; ?></a>
										</li>
									<? } ?>
								</ul>
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>

	<script type="text/javascript">//<!--
		$('#controller_override_list').ac_template('co_list', {defaults: <?= json_encode($controller_overrides['__ac_template__']); ?>});
		$('#add_override').click(function () {
			$.ac_template('co_list', 'add')
		});
//--></script>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
