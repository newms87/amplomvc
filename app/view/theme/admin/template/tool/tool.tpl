<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/backup.png'); ?>" alt=""/> {{System Tools}}</h1>

			<div class="buttons">
				<a href="<?= $cancel; ?>" class="button">{{Cancel}}</a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $clear_cache; ?>" method="post" enctype="multipart/form-data" id="clear_cache">
				<table class="form">
					<tr>
						<td>
							<div>{{Clear Specified Cache Table(s):}}</div>
							<span class="help">{{Use a regular expression to clear cache table entries. The regular expression is prepended with '^' and appended with '*' automatically. For example, if you want to clear entries starting with product, just enter 'product'. To clear all entries, enter '.' or '.*'}}</span>
						</td>
						<td>
							<label for="cache_tables">{{Cache Tables:}}</label>
							<input type="text" name="cache_tables" value="<?= $cache_tables; ?>"/>
							<input type="submit" class="button" value="{{Clear Cache Entries}}"/>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $is_ajax ? '' : call('admin/footer'); ?>
