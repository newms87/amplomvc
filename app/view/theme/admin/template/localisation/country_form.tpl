<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<? if ($error_warning) { ?>
		<div class="message warning"><?= $error_warning; ?></div>
	<? } ?>
	<div class="box">
		<div class="heading">
			<div class="breadcrumbs">
				<?= $is_ajax ? '' : breadcrumbs(); ?>
			</div>

			<h1><img src="<?= theme_url('image/country.png'); ?>" alt=""/> {{Country}}</h1>

			<div class="buttons"><a onclick="$('#form').submit();" class="button">{{Save}}</a><a
					href="<?= $cancel; ?>" class="button">{{Cancel}}</a></div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> {{Country Name:}}</td>
						<td><input type="text" name="name" value="<?= $name; ?>"/>
							<? if (_l("Country Name must be between 3 and 128 characters!")) { ?>
								<span class="error">{{Country Name must be between 3 and 128 characters!}}</span>
							<? } ?></td>
					</tr>
					<tr>
						<td>{{ISO Code (2):}}</td>
						<td><input type="text" name="iso_code_2" value="<?= $iso_code_2; ?>"/></td>
					</tr>
					<tr>
						<td>{{ISO Code (3):}}</td>
						<td><input type="text" name="iso_code_3" value="<?= $iso_code_3; ?>"/></td>
					</tr>
					<tr>
						<td>{{Address Format:}}
							<br/>
							<span class="help">
								Name = {name}<br/>
								Company = {company}<br/>
								Address 1 = {address}<br/>
								Address 2 = {address_2}<br/>
								City = {city}<br/>
								Postcode = {postcode}<br/>
								Zone = {zone}<br/>
								Zone Code = {zone_code}<br/>
								Country = {country}
							</span>
						</td>
						<td><textarea name="address_format" cols="40" rows="5"><?= $address_format; ?></textarea></td>
					</tr>
					<tr>
						<td>{{Postcode Required:}}</td>
						<td><? if ($postcode_required) { ?>
								<input type="radio" name="postcode_required" value="1" checked="checked"/>
								{{Yes}}
								<input type="radio" name="postcode_required" value="0"/>
								{{No}}
							<? } else { ?>
								<input type="radio" name="postcode_required" value="1"/>
								{{Yes}}
								<input type="radio" name="postcode_required" value="0" checked="checked"/>
								{{No}}
							<? } ?></td>
					</tr>
					<tr>
						<td>{{Status:}}</td>
						<td><select name="status">
								<? if ($status) { ?>
									<option value="1" selected="selected">{{Enabled}}</option>
									<option value="0">{{Disabled}}</option>
								<? } else { ?>
									<option value="1">{{Enabled}}</option>
									<option value="0" selected="selected">{{Disabled}}</option>
								<? } ?>
							</select></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
</div>
<?= $is_ajax ? '' : call('admin/footer'); ?>
