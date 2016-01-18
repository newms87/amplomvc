<?= $is_ajax ? '' : call('admin/header'); ?>
<section class="section">
	<div class="dashboard-header">
		<div class="breadcrumbs col xs-12 md-6 left">
			<?= $is_ajax ? '' : breadcrumbs(); ?>
		</div>
	</div>

	<div class="row dashboard-title">
		<h1 class="col auto dashboard-name" data-orig="<?= $title; ?>" <?= $can_edit ? 'contenteditable' : ''; ?>><?= $title; ?></h1>
	</div>

	<div class="dashboard-view">
		<?= block('widget/views', null, array('group' => $group)); ?>
	</div>
</section>

<? if ($can_edit) { ?>
	<script type="text/javascript">
		$('.dashboard-name').blur(function() {
			var $this = $(this);
			if ($this.attr('data-orig') != $this.html()) {
				$this.attr('data-orig', $this.html());
				var data = {
					title: $this.html()
				};
				$.post("<?= site_url('admin/dashboard/save', 'dashboard_id=' . $dashboard_id); ?>", data, function(response) {
					$('.dashboard-header').show_msg(response);
				}, 'json');
			}
		});
	</script>
<? } ?>

<?= $is_ajax ? '' : call('admin/footer'); ?>
