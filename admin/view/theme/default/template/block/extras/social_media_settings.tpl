<table class="form">
	<tr>
		<td>
			<?= _l("Your Social Networks:"); ?>
			<span class="help"><?= _l("Include the http:// or https://"); ?></span>

			<div id="add_network" onclick="add_network()"><?= _l("Add Network"); ?></div>
		</td>
		<td>
			<ul id="social_network_list">
				<? $network_id = 0; ?>
				<? foreach ($networks as $network) { ?>
					<li class="social_network">
					<span class="social_icon">
						<input type="text" class="imageinput" name="settings[networks][<?= $network_id; ?>][icon]" value="<?= $network['icon']; ?>" data-thumb="<?= $network['thumb']; ?>" />
					</span>
					<span class="social_url">
						<input type="text" name="settings[networks][<?= $network_id; ?>][href]" value="<?= $network['href']; ?>"/>
					</span>
						<img src="<?= theme_url('image/delete.png'); ?>" class="delete" onclick="$(this).parent().remove()"/>
					</li>
					<? $network_id++; ?>
				<? } ?>
			</ul>
		</td>
	</tr>
</table>

<script type="text/javascript">
	$('.imageinput').ac_imageinput();

//TODO: implement AC Template
	$('#social_network_list').sortable();

	$('#form').submit(function () {
		$('#network_template').remove();
	});
</script>
