<?= $header; ?>
<div class="content">
	<?= $this->builder->display_breadcrumbs();?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'home.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
		</div>
		<div class="content">
				<div class='dashboard-heading'><?= $text_portal_welcome;?></div>
				<div class='dashboard-content'><?= $text_portal_description;?></div>
		</div>
	</div>
</div>
<?= $footer; ?>