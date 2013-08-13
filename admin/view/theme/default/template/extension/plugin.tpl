<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt="" /> <?= $head_title; ?></h1>
		</div>
		<div class="content">
			<table class="list">
				<thead>
					<tr>
						<td class="left"><?= $column_name; ?></td>
						<td class="right"><?= $column_action; ?></td>
					</tr>
				</thead>
				<tbody>
					<? if ($plugins) { ?>
					<? foreach ($plugins as $plugin) { ?>
					<tr>
						<td class="left"><?= $plugin['name']; ?></td>
						<td class="right">
							<? foreach ($plugin['action'] as $action) { ?>
							[ <a href="<?= $action['href']; ?>" class="<?= $this->tool->getSlug($action['text']); ?>"><?= $action['text']; ?></a> ]
							<? } ?>
						</td>
					</tr>
					<? } ?>
					<? } else { ?>
					<tr>
						<td class="center" colspan="8"><?= $text_no_results; ?></td>
					</tr>
					<? } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?= $this->builder->js('errors', $errors); ?>

<script type="text/javascript">//<!--
$('.uninstall').click(function(){
	keep_data = 0;
	
	if(confirm("<?= $text_keep_data; ?>")){
		keep_data = 1;
	}
	
	$(this).attr('href', $(this).attr('href') + '&keep_data=' + keep_data);
});
//--></script>
<?= $footer; ?>