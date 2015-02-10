<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section clear">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<div class="box">
		<form action="<?= $action; ?>" method="post" id="forgotten">
			<div class="heading">
				<h1><img src="<?= theme_url('image/user.png'); ?>" alt=""/> {{Forgot Your Password?}}</h1>

				<div class="buttons">
					<button>{{Reset}}</button>
					<a href="<?= $cancel; ?>" class="button">{{Cancel}}</a>
				</div>
			</div>

			<div class="section">
				<p>{{Enter the e-mail address associated with your account. Click submit to have a password reset link e-mailed to you.}}</p>
				<table class="form">
					<tr>
						<td>{{E-Mail Address:}}</td>
						<td><input type="text" name="email" value="<?= $email; ?>"/></td>
					</tr>
				</table>
			</div>
		</form>
	</div>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
