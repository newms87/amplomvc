<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<div id="tab-general">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_keyword; ?></td>
							<td><input type="text" name="keyword" value="<?= $keyword; ?>" size="40" /></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_route; ?></td>
							<td><input type="text" name="route" value="<?= $route; ?>" size="40" />
						</tr>
						<tr>
							<td class="required"> <?= $entry_query; ?></td>
							<td><input type="text" name="query" value="<?= $query; ?>" size="40" />
						</tr>
						<tr>
							<td class="required"> <?= $entry_redirect; ?></td>
							<td><input type="text" name="redirect" value="<?= $redirect; ?>" size="40" />
						</tr>
						<tr>
							<td><?= $entry_admin; ?></td>
							<td><?= $this->builder->build('select',$data_yes_no, 'admin',$admin); ?></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><?= $this->builder->build('select',$statuses, 'status',$status); ?></td>
						</tr>
					</table>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->builder->js('errors',$errors); ?>

<?= $footer; ?>