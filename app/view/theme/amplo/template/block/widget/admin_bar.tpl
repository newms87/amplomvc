<div id="admin-bar" class="clearfix">
	<a href="<?= $admin_link; ?>" target="_blank" class="admin-link">{{Admin Panel}}</a>

	<div class="clock">
		<?= $clock_time; ?>
		<a class="sim-time back" href="<?= $sim_back; ?>"></a>
		<a class="sim-time reset" href="<?= $sim_reset; ?>"></a>
		<a class="sim-time forward" href="<?= $sim_forward; ?>"></a>
	</div>

	<a id="disable-admin-bar">{{X}}</a>
</div>

<script type="text/javascript">
	$('#disable-admin-bar').click(function () {
		$.cookie('<?= COOKIE_PREFIX . 'disable_admin_bar'; ?>, '1');
		$(this).closest('#admin-bar').remove();
		$('body').removeClass("admin-bar");
	});
</script>
