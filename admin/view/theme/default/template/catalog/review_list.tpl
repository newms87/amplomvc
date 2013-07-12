<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<? if ($error_warning) { ?>
	<div class="message_box warning"><?= $error_warning; ?></div>
	<? } ?>
	<? if ($success) { ?>
	<div class="message_box success"><?= $success; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= HTTP_THEME_IMAGE . 'review.png'; ?>" alt="" /> <?= $heading_title; ?></h1>
			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= $button_insert; ?></a><a onclick="$('form').submit();" class="button"><?= $button_delete; ?></a></div>
		</div>
		<div class="content">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
							<td class="left"><? if ($sort == 'pd.name') { ?>
								<a href="<?= $sort_product; ?>" class="<?= strtolower($order); ?>"><?= $column_product; ?></a>
								<? } else { ?>
								<a href="<?= $sort_product; ?>"><?= $column_product; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'r.author') { ?>
								<a href="<?= $sort_author; ?>" class="<?= strtolower($order); ?>"><?= $column_author; ?></a>
								<? } else { ?>
								<a href="<?= $sort_author; ?>"><?= $column_author; ?></a>
								<? } ?></td>
							<td class="right"><? if ($sort == 'r.rating') { ?>
								<a href="<?= $sort_rating; ?>" class="<?= strtolower($order); ?>"><?= $column_rating; ?></a>
								<? } else { ?>
								<a href="<?= $sort_rating; ?>"><?= $column_rating; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'r.status') { ?>
								<a href="<?= $sort_status; ?>" class="<?= strtolower($order); ?>"><?= $column_status; ?></a>
								<? } else { ?>
								<a href="<?= $sort_status; ?>"><?= $column_status; ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'r.date_added') { ?>
								<a href="<?= $sort_date_added; ?>" class="<?= strtolower($order); ?>"><?= $column_date_added; ?></a>
								<? } else { ?>
								<a href="<?= $sort_date_added; ?>"><?= $column_date_added; ?></a>
								<? } ?></td>
							<td class="right"><?= $column_action; ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($reviews) { ?>
						<? foreach ($reviews as $review) { ?>
						<tr>
							<td style="text-align: center;"><? if ($review['selected']) { ?>
								<input type="checkbox" name="selected[]" value="<?= $review['review_id']; ?>" checked="checked" />
								<? } else { ?>
								<input type="checkbox" name="selected[]" value="<?= $review['review_id']; ?>" />
								<? } ?></td>
							<td class="left"><?= $review['name']; ?></td>
							<td class="left"><?= $review['author']; ?></td>
							<td class="right"><?= $review['rating']; ?></td>
							<td class="left"><?= $review['status']; ?></td>
							<td class="left"><?= $review['date_added']; ?></td>
							<td class="right"><? foreach ($review['action'] as $action) { ?>
								[ <a href="<?= $action['href']; ?>"><?= $action['text']; ?></a> ]
								<? } ?></td>
						</tr>
						<? } ?>
						<? } else { ?>
						<tr>
							<td class="center" colspan="7"><?= $text_no_results; ?></td>
						</tr>
						<? } ?>
					</tbody>
				</table>
			</form>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<?= $footer; ?>