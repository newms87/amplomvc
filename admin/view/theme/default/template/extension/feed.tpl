<?= $header; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<? if ($success) { ?>
			<div class="message_box success"><?= $success; ?></div>
		<? } ?>
		<? if ($error) { ?>
			<div class="message_box warning"><?= $error; ?></div>
		<? } ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'feed.png'; ?>" alt=""/> <?= $head_title; ?></h1>
			</div>
			<div class="content">
				<table class="list">
					<thead>
					<tr>
						<td class="left"><?= $column_name; ?></td>
						<td class="left"><?= $column_status; ?></td>
						<td class="right"><?= $column_action; ?></td>
					</tr>
					</thead>
					<tbody>
					<? if ($extensions) { ?>
						<? foreach ($extensions as $extension) { ?>
							<tr>
								<td class="left"><?= $extension['name']; ?></td>
								<td class="left"><?= $extension['status'] ?></td>
								<td class="right"><? foreach ($extension['action'] as $action) { ?>
										[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
									<? } ?></td>
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
<?= $footer; ?>