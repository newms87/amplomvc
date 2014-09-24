<?= IS_AJAX ? '' : call('admin/common/header'); ?>
<section class="section">
	<?= IS_AJAX ? '' : breadcrumbs(); ?>

	<div class="dashboard-header">
		<h2 class="dashboard-name" data-orig="<?= $title; ?>" <?= $can_edit ? 'contenteditable' : ''; ?>><?= $title; ?></h2>
	</div>

	<div class="dashboard-view">
		<?= block('widget/views', null, array('group' => $group)); ?>
	</div>
</section>

<? if ($can_edit) { ?>
<script type="text/javascript">
	$('.dashboard-name').blur(function () {
		var $this = $(this);
		if ($this.attr('data-orig') != $this.html()) {
			$this.attr('data-orig', $this.html());
			var data = {
				title: $this.html()
			};
			$.post("<?= site_url('admin/dashboard/save', 'dashboard_id=' . $dashboard_id); ?>", data, function (response) {
				$('.dashboard-header').ac_msg(response);
			}, 'json');
		}
	});
</script>
<? } ?>

<?= IS_AJAX ? '' : call('admin/common/footer'); ?>
