<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>

		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'language.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a
						href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
			</div>
			<div class="section">
				<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_name; ?></td>
							<td><input type="text" name="name" value="<?= $name; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_code; ?></td>
							<td><input type="text" name="code" value="<?= $code; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_locale; ?></td>
							<td><input type="text" name="locale" value="<?= $locale; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_datetime_format; ?></td>
							<td><input type="text" name="datetime_format" value="<?= $datetime_format; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_date_format_short; ?></td>
							<td><input type="text" name="date_format_short" value="<?= $date_format_short; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_date_format_long; ?></td>
							<td><input type="text" name="date_format_long" value="<?= $date_format_long; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_time_format; ?></td>
							<td><input type="text" name="time_format" value="<?= $time_format; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_direction; ?></td>
							<td><?= $this->builder->build('select', $data_direction, "direction", $direction); ?></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_decimal_point; ?></td>
							<td><input type="text" style='font-size:30px' size='1' name="decimal_point" value="<?= $decimal_point; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_thousand_point; ?></td>
							<td><input type="text" style='font-size:30px' size='1' name="thousand_point" value="<?= $thousand_point; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_image; ?></td>
							<td><input type="text" name="image" value="<?= $image; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_directory; ?></td>
							<td><input type="text" name="directory" value="<?= $directory; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_filename; ?></td>
							<td><input type="text" name="filename" value="<?= $filename; ?>"/></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><?= $this->builder->build('select', $data_statuses, "status", (int)$status); ?></td>
						</tr>
						<tr>
							<td><?= $entry_sort_order; ?></td>
							<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1"/></td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
<?= $footer; ?>