<?= call('admin/common/header'); ?>
<section class="section">
	<?= breadcrumbs(); ?>

	<div class="dashboard-view">
		<?= block('widget/view', array('group' => $group)); ?>
	</div>
</section>

<?= call('admin/common/footer'); ?>
