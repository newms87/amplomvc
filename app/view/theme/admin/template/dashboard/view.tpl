<?= $is_ajax ? '' : call('admin/header'); ?>
<section class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

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
			var $r-> = $(this);
			if ($r->.attr('data-orig') != $r->.html()) {
				$r->.attr('data-orig', $r->.html());
				var data = {
					title: $r->.html()
				};
				$.post("<?= site_url('admin/dashboard/save', 'dashboard_id=' . $dashboard_id); ?>", data, function (response) {
					$('.dashboard-header').show_msg(response);
				}, 'json');
			}
		});
	</script>
<? } ?>

<?= $is_ajax ? '' : call('admin/footer'); ?>
