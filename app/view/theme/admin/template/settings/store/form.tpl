<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" class="box">
		<div class="heading">
			<h1>
				<img src="<?= theme_url('image/setting.png'); ?>" alt=""/> <?= $name; ?>
			</h1>

			<div class="buttons">
				<button>{{Save}}</button>
				<a href="<?= site_url('admin/settings'); ?>" class="button">{{Cancel}}</a>
			</div>
		</div>
		<div class="section">
			<table class="form">
				<tr>
					<td class="required"> {{Store Name:}}</td>
					<td>
						<input type="text" name="name" value="<?= $name; ?>" size="40"/>
					</td>
				</tr>
				<tr>
					<td class="required"> <?= _l("Store URL:<br /><span class=\"help\">Include the full URL to your store. Make sure to add \'/\' at the end. Example: http://www.yourdomain.com/path/<br /><br />Don\'t use directories to create a new store. You should always point another domain or sub domain to your hosting.</span>"); ?></td>
					<td>
						<input type="text" name="url" value="<?= $url; ?>" size="40"/>
					</td>
				</tr>
				<tr>
					<td><?= _l("SSL URL:<br /><span class=\"help\">SSL URL to your store. Make sure to add \'/\' at the end. Example: http://www.yourdomain.com/path/<br /><br />Don\'t use directories to create a new store. You should always point another domain or sub domain to your hosting.</span>"); ?></td>
					<td>
						<input type="text" name="ssl" value="<?= $ssl; ?>" size="40"/>
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>

<script type="text/javascript">
	$.ac_errors(<?= json_encode($errors); ?>);
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
