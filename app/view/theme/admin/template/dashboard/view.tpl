<?= call('admin/common/header'); ?>
<section class="section">
	<?= breadcrumbs(); ?>

	<div class="dashboard-header">
		<h2 class="dashboard-name" data-orig="<?= $name; ?>" contenteditable><?= $name; ?></h2>
	</div>

	<div class="dashboard-view">
		<?= block('widget/views', null, array('group' => $group)); ?>
	</div>
</section>

<script type="text/javascript">
	$('.dashboard-name').blur(function(){
		var $this = $(this);
		if ($this.attr('data-orig') != $this.html()) {
			var data = {
				name: $this.html()
			};
			$.post("<?= site_url('admin/dashboard/save', 'dashboard_id=' . $dashboard_id); ?>", data, function (response){
				$('.dashboard-header').ac_msg(response);
			}, 'json');
		}
	});
</script>

<?= call('admin/common/footer'); ?>