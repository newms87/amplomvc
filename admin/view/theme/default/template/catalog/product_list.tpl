<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="batch_actions">
				<?= $this->builder->build_batch_actions('#form', $batch_actions, $batch_update); ?>
			</div>
			<div class="buttons">
				<a href="<?= $insert; ?>" class="button"><?= $button_insert; ?></a>
				<a onclick="do_batch_action('copy')" class="button"><?= $button_copy; ?></a>
			</div>
		</div>
		<div class="content">
			<form action="" method="post" id="form">
				<?= $list_view; ?>
			</form>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>

<?= $footer; ?>