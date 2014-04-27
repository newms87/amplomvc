<?= _call('common/header'); ?>
<div class="section">
	<?= _breadcrumbs(); ?>
	<div class="box">
		<div class="heading">
			<h1><img src="<?= URL_THEME_IMAGE . 'review.png'; ?>" alt=""/> <?= _l("Reviews"); ?></h1>

			<div class="buttons"><a onclick="location = '<?= $insert; ?>'" class="button"><?= _l("Insert"); ?></a><a onclick="$('form').submit();" class="button"><?= _l("Delete"); ?></a></div>
		</div>
		<div class="section">
			<form action="<?= $delete; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="list">
					<thead>
						<tr>
							<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);"/>
							</td>
							<td class="left"><? if ($sort == 'pd.name') { ?>
									<a href="<?= $sort_product; ?>"
										class="<?= strtolower($order); ?>"><?= _l("Product"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_product; ?>"><?= _l("Product"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'r.author') { ?>
									<a href="<?= $sort_author; ?>" class="<?= strtolower($order); ?>"><?= _l("Author"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_author; ?>"><?= _l("Author"); ?></a>
								<? } ?></td>
							<td class="right"><? if ($sort == 'r.rating') { ?>
									<a href="<?= $sort_rating; ?>" class="<?= strtolower($order); ?>"><?= _l("Rating"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_rating; ?>"><?= _l("Rating"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'r.status') { ?>
									<a href="<?= $sort_status; ?>" class="<?= strtolower($order); ?>"><?= _l("Status"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_status; ?>"><?= _l("Status"); ?></a>
								<? } ?></td>
							<td class="left"><? if ($sort == 'r.date_added') { ?>
									<a href="<?= $sort_date_added; ?>"
										class="<?= strtolower($order); ?>"><?= _l("Date Added"); ?></a>
								<? } else { ?>
									<a href="<?= $sort_date_added; ?>"><?= _l("Date Added"); ?></a>
								<? } ?></td>
							<td class="right"><?= _l("Action"); ?></td>
						</tr>
					</thead>
					<tbody>
						<? if ($reviews) { ?>
							<? foreach ($reviews as $review) { ?>
								<tr>
									<td style="text-align: center;"><? if ($review['selected']) { ?>
											<input type="checkbox" name="batch[]" value="<?= $review['review_id']; ?>"
												checked="checked"/>
										<? } else { ?>
											<input type="checkbox" name="batch[]" value="<?= $review['review_id']; ?>"/>
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
								<td class="center" colspan="7"><?= _l("There are no results to display."); ?></td>
							</tr>
						<? } ?>
					</tbody>
				</table>
			</form>
			<div class="pagination"><?= $pagination; ?></div>
		</div>
	</div>
</div>
<?= _call('common/footer'); ?>
