<?= $header; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'setting.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="batch_actions">
					<a href="<?= $download_email_list; ?>" class="button email_list"><?= $button_email_list; ?></a>
					<?= $this->builder->batch_action('#listing [name="selected[]"]', $batch_actions, $batch_update); ?>
				</div>
				<div class="buttons">
					<a href="<?= $insert; ?>" class="button"><?= $button_insert; ?></a>
					<a onclick="do_batch_action('copy')" class="button"><?= $button_copy; ?></a>
				</div>
			</div>
			<div class="content">
				<form action="" method="post" id="form">
					<?= $newsletter_view; ?>
				</form>
				<div class="pagination"><?= $pagination; ?></div>
			</div>
		</div>
	</div>

<?= $footer; ?>