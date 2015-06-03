<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<?= $is_ajax ? '' : breadcrumbs(); ?>

	<div class="box">
		<div class="heading">
			<h1><img src="<?= theme_url('image/country.png'); ?>" alt=""/> {{Geo Zones}}</h1>

			<div class="buttons">
				<a onclick="$('#form').submit();" class="button">{{Save}}</a>
				<a href="<?= $cancel; ?>" class="button">{{Cancel}}</a>
			</div>
		</div>
		<div class="section">
			<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<table class="form">
					<tr>
						<td class="required"> {{Geo Zone Name:}}</td>
						<td><input type="text" name="name" value="<?= $name; ?>"/></td>
					</tr>
					<tr>
						<td class="required"> {{Description:}}</td>
						<td><input type="text" name="description" value="<?= $description; ?>"/></td>
					</tr>
					<tr>
						<td>{{Exclude Zones:}}</td>
						<td><input type="checkbox" name="exclude" value="1" <?= $exclude ? 'checked="checked"' : ''; ?> />
						</td>
					</tr>
				</table>
				<br/>
				<table id="zone-to-geo-zone" class="list">
					<thead>
					<tr>
						<td class="left">{{Country:}}</td>
						<td class="left">{{Zone:}}</td>
						<td></td>
					</tr>
					</thead>


					<? $zones['template_row'] = array(
						"country_id" => "%country_id%",
						"zone_id"    => "%zone_id%",
					); ?>

					<tbody id="zone_list">
					<? $zone_row = 0; ?>
					<? foreach ($zones as $key => $zone) { ?>
						<? $row = $key == 'template_row' ? '%zone_row%' : $zone_row++; ?>
						<tr class="geozone_selector <?= $key; ?>">
							<td class="left">
								<?= build(array(
									'type' => 'select',
									'name' => "zones[$row][country_id]",
									'data' => $data_countries,
									'select' => $zone['country_id'],
									array('class' => 'country_selector'),
									'value' => 'country_id',
									'label' => 'name',
								)); ?>
								<a onclick="add_all_zones($(this))"
									style="text-decoration:none; display:block">{{[ Add All Zones ]}}</a>
							</td>
							<td class="left">
								<select name="zones[<?= $row; ?>][zone_id]" data-zone_id="<?= $zone['zone_id']; ?>"
									class="zone_selector"></select>
							</td>
							<td class="left"><a onclick="$(this).closest('.geozone_selector').remove();"
									class="button">{{Remove}}</a></td>
						</tr>
					<? } ?>
					</tbody>

					<tfoot>
					<tr>
						<td colspan="2"></td>
						<td class="left"><a onclick="addZoneRow();" class="button">{{Add Geo Zone}}</a></td>
					</tr>
					</tfoot>
				</table>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('.geozone_selector .zone_selector').ac_zoneselect({
		listen:    '.geozone_selector .country_selector',
		allow_all: true
	});

	function add_all_zones(context) {
		country_id = context.closest('.geozone_selector').find('.country_selector').val();
		zone_id = context.closest('.geozone_selector').find('.zone_selector').val();
		zones = context.closest('.geozone_selector').find('.zone_selector').children();

		context.hide().fadeIn(600);

		if (zones.length < 1) return;

		zones.each(function (i, e) {
			add = true;
			value = $(e).attr('value');

			$('.zone_selector').each(function (j, z) {
				if ($(z).val() == value) {
					add = false;
					return false;
				}
			});

			if (add && value != zone_id && value != '0' && value) {
				addZoneRow(country_id, value);
			}
		});
	}
</script>

<script type="text/javascript">
	var temp = $('#zone_list').find('.template_row');
	var zone_template = temp.html();
	temp.remove();

	var zone_row = <?= $zone_row; ?>;

	function addZoneRow(country_id, zone_id) {
		country_id = country_id || 1;
		zone_id = zone_id || 0;

		template = zone_template
			.replace(/%zone_row%/g, zone_row)
			.replace(/%country_id%/g, 0)
			.replace(/%zone_id%/g, zone_id);

		$('#zone_list').append($('<tr class="geozone_selector ' + zone_row + '" />').append(template));

		$('.geozone_selector.' + zone_row).find('.country_selector').val(country_id + 1).change().val(country_id).change();

		zone_row++;
	}
	;
</script>



<?= $is_ajax ? '' : call('admin/footer'); ?>
