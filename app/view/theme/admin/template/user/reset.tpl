<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section clear">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/user.png'); ?>" alt=""/> {{Reset Your Password}}</h1>

			<div class="buttons">
				<a href="<?= $cancel; ?>" class="button">{{Cancel}}</a>
			</div>
		</div>

		<div class="section">
			<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="reset">
				<h2>{{Enter your new Password:}}</h2>
				<table class="form">
					<tr>
						<td>{{Password:}}</td>
						<td><input type="password" autocomplete="off" name="password" value=""/></td>
					</tr>
					<tr>
						<td>{{Password Confirmation:}}</td>
						<td><input type="password" name="confirm" value=""/></td>
					</tr>
				</table>

				<input type="submit" class="button" value="{{Change Password}}"/>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
