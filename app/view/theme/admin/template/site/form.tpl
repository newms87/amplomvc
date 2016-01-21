<?= $is_ajax ? '' : call('admin/header'); ?>

<div class="section">
	<form action="<?= site_url('admin/site/save', 'site_id=' . $site_id); ?>" method="post" enctype="multipart/form-data" class="box">
		<div class="heading">
			<div class="breadcrumbs col xs-12 md-6 left">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<div class="buttons col xs-12 md-6 md-right">
				<button>{{Save}}</button>
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
					<td class="required">
						{{Domain:}}
						<div class="help">{{The Domain for your site. When a user connects to your server, Amplo will use the domain to determine which site to show the user. Example: my-site.com}}</div>
					</td>
					<td>
						<input type="text" name="domain" value="<?= $domain; ?>" size="40"/>
					</td>
				</tr>
				<tr>
					<td class="required">
						{{Prefix:}}
						<div class="help">{{The Prefix is primarily an identifier for a group of sites using the same database. The Prefix will be appended to distinct database tables for this site. A unique prefix here will ensure unique settings for this site.}}</div>
					</td>
					<td>
						<input type="text" name="prefix" value="<?= $prefix; ?>" size="2"/>
					</td>
				</tr>
				<tr>
					<td class="required">
						{{Store URL:}}
						<div class="help">{{The URL for your site. Should end with a '/'. Example: http://www.my-site.com/path/}}</div>
					</td>
					<td>
						<input type="text" name="url" value="<?= $url; ?>" size="40"/>
					</td>
				</tr>
				<tr>
					<td>
						{{Site Secure URL:}}
						<div class="help">{{The Secure URL for your site. Should end with a '/'. Example: https://www.my-site.com/path/}}</div>
					</td>
					<td>
						<input type="text" name="ssl" value="<?= $ssl; ?>" size="40"/>
					</td>
				</tr>
			</table>
		</div>
	</form>
</div>

<?= $is_ajax ? '' : call('admin/footer'); ?>
