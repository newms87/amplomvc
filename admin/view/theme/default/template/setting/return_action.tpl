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
							<td valign="top"><a id="add_action" class="button"><?= $button_add; ?></a></td>
							<td>
								<ul id="return_action_list" class="easy_list">
									<? foreach ($return_actions as $row => $action) { ?>
										<li class="return_action" data-row="<?= $row; ?>">
											<input class="title" size="50" type="text" name="return_actions[<?= $row; ?>][title]" value="<?= $action['title']; ?>"/><br/>
											<? if (empty($action['no_delete'])) { ?>
												<a class="delete button text" onclick="$(this).closest('li').remove()"><?= $button_delete; ?></a>
											<? } ?>
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

<? foreach ($return_actions as $key => $action) { ?>
	<?= $this->builder->js('translations', $action['translations'], "return_actions[$key][%name%]"); ?>
<? } ?>

	<script type="text/javascript">//<!--
		$('#return_action_list').ac_template('ra_list', {defaults: <?= json_encode($return_actions['__ac_template__']); ?>});
		$('#add_action').click(function () {
			$.ac_template('ra_list', 'add')
		});

		$('#return_action_list').sortable();
//--></script>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>
