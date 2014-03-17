<?= $common_header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= URL_THEME_IMAGE . 'error.png'; ?>" alt=""/> <?= _l("Permission Denied!"); ?></h1>
			</div>
			<div class="section">
				<div
					style="border: 1px solid #DDDDDD; background: #F7F7F7; text-align: center; padding: 15px;"><?= _l("You do not have permission to access this page, please refer to your system administrator."); ?></div>
			</div>
		</div>
	</div>
<?= $common_footer; ?>
