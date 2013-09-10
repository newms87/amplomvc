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
							<td valign="top"><a id="add_status" class="button"><?= $button_add; ?></a></td>
							<td>
								<ul id="return_status_list" class="easy_list">
									<? foreach ($return_statuses as $row => $status) { ?>
										<li class="return_status" data-row="<?= $row; ?>">
											<input class="title" size="50" type="text" name="return_statuses[<?= $row; ?>][title]" value="<?= $status['title']; ?>"/><br/>
											<? if (empty($status['no_delete'])) { ?>
												<a class="delete_button text" onclick="$(this).closest('li').remove()"><?= $button_delete; ?></a>
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

<? foreach ($return_statuses as $key => $status) { ?>
	<?= $this->builder->js('translations', $status['translations'], "return_statuses[$key][%name%]"); ?>
<? } ?>

	<script type="text/javascript">//<!--
		$('#return_status_list').ac_template('rs_list', {defaults: <?= json_encode($return_statuses['__ac_template__']); ?>});
		$('#add_status').click(function () {
			$.ac_template('rs_list', 'add')
		});

		$('#return_status_list').sortable();
//--></script>

<?= $this->builder->js('errors', $errors); ?>

<?= $footer; ?>