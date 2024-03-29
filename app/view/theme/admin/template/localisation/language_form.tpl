<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<h1><img src="<?= theme_url('image/language.png'); ?>" alt=""/> {{Language}}</h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button">{{Save}}</a><a
					href="<?= $cancel; ?>" class="button">{{Cancel}}</a></div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> {{Language Name:}}</td>
						<td><input type="text" name="name" value="<?= $name; ?>"/></td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Code:<br /><span class=\"help\">eg: en. Do not change if this is your default language.</span>"); ?></td>
						<td><input type="text" name="code" value="<?= $code; ?>"/></td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Locale:<br /><span class=\"help\">eg: en_US.UTF-8,en_US,en-gb,en_gb,english</span>"); ?></td>
						<td><input type="text" name="locale" value="<?= $locale; ?>"/></td>
					</tr>
					<tr>
						<td class="required"> {{Datetime Format:}}</td>
						<td><input type="text" name="datetime_format" value="<?= $datetime_format; ?>"/></td>
					</tr>
					<tr>
						<td class="required"> {{Short Date Format:}}</td>
						<td><input type="text" name="date_format_short" value="<?= $date_format_short; ?>"/></td>
					</tr>
					<tr>
						<td class="required"> {{Long Date Format:}}</td>
						<td><input type="text" name="date_format_long" value="<?= $date_format_long; ?>"/></td>
					</tr>
					<tr>
						<td class="required"> {{Time Format:}}</td>
						<td><input type="text" name="time_format" value="<?= $time_format; ?>"/></td>
					</tr>
					<tr>
						<td class="required"> {{Reading Direction:}}</td>
						<td><?= build(array(
								'type'   => 'select',
								'name'   => "direction",
								'data'   => $data_direction,
								'select' => $direction
							)); ?></td>
					</tr>
					<tr>
						<td class="required"> {{Decimal Point:}}</td>
						<td>
							<input type="text" style="font-size:30px" size="1" name="decimal_point" value="<?= $decimal_point; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required"> {{Thousand Point:}}</td>
						<td>
							<input type="text" style="font-size:30px" size="1" name="thousand_point" value="<?= $thousand_point; ?>"/>
						</td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Image:<br /><span class=\"help\">eg: gb.png</span>"); ?></td>
						<td><input type="text" name="image" value="<?= $image; ?>"/></td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Directory:<br /><span class=\"help\">name of the language directory (case-sensitive)</span>"); ?></td>
						<td><input type="text" name="directory" value="<?= $directory; ?>"/></td>
					</tr>
					<tr>
						<td class="required"> <?= _l("Filename:<br /><span class=\"help\">main language filename without extension</span>"); ?></td>
						<td><input type="text" name="filename" value="<?= $filename; ?>"/></td>
					</tr>
					<tr>
						<td><?= _l("Status:<br /><span class=\"help\">Hide/Show it in language dropdown</span>"); ?></td>
						<td><?= build(array(
								'type'   => 'select',
								'name'   => "status",
								'data'   => $data_statuses,
								'select' => $status
							)); ?></td>
					</tr>
					<tr>
						<td>{{Sort Order:}}</td>
						<td><input type="text" name="sort_order" value="<?= $sort_order; ?>" size="1"/></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $is_ajax ? '' : call('admin/footer'); ?>
