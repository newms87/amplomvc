<?= call('mail/header', $header); ?>

<p style="margin-top: 0px; margin-bottom: 20px; font-size: 2em">
	<?= _l("%s", $subject); ?>
</p>

<table style="width: 800px" cellpadding="20">

	<? foreach ($views as $view) { ?>
		<tr>
			<td style="text-align: center">
				<h3><?= $view['title']; ?></h3>
				<br/>

				<? if (!empty($view['image'])) { ?>
					<img src="<?= cast_protocol($view['image']); ?>" width="800" height="400"/>
				<? } elseif (!empty($view['data'])) { ?>
					<table style="text-align:left" cellpadding="3">
						<thead>
							<tr style="padding-top:1em;padding-bottom:1em;font-size:1.2em;font-weight:bold;">
								<? foreach (array_keys($view['data']['records'][0]) as $col) { ?>
									<td><?= $col; ?></td>
								<? } ?>
							</tr>
						</thead>
						<tbody>
							<? foreach ($view['data']['records'] as $row) { ?>
								<tr>
									<? foreach ($row as $col => $data) { ?>
										<td><?= $data; ?></td>
									<? } ?>
								</tr>
							<? } ?>
						</tbody>

						<? $remaining = $view['data']['total_records'] - count($view['data']['records']); ?>
						<? if ($remaining > 0) { ?>
							<tfoot>
								<tr>
									<td colspan="<?= count($view['data']['records'][0]); ?>" style="text-align:center">
										<a href="<?= site_url('admin/dashboard/view', 'dashboard_id=' . $dashboard['dashboard_id']); ?>"><?= _l("And %s more...", $remaining); ?></a>
									</td>
								</tr>
							</tfoot>
						<? } ?>

					</table>
				<? } else { ?>
					<p><?= _l("There is no data to show for this report"); ?></p>
				<? } ?>
				<br/>
				<br/>
			</td>
		</tr>
	<? } ?>

</table>

<?= call('mail/footer'); ?>
