<?= call('common/header'); ?>
<?= area('left'); ?><?= area('right'); ?>

<div class="content">
	<?= breadcrumbs(); ?>

	<div class="section">

		<h1><?= _l("Example Page Title"); ?></h1>

		<?= area('top'); ?>

		<div class="page-content">
			<?= _l("Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum."); ?>
		</div>
	</div>

	<?= area('bottom'); ?>
</div>

<?= call('common/footer'); ?>
