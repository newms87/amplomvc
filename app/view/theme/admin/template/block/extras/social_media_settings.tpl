<table class="form">
	<tr>
		<td>
			{{Your Social Networks:}}
			<span class="help">{{Include the http:// or https://}}</span>

			<div id="add_network">{{Add Network}}</div>
		</td>
		<td>
			<ul id="social_network_list">
				<? foreach ($networks as $row => $network) { ?>
					<li class="social_network" data-row="<?= $row; ?>">
						<span class="social_icon">
							<input type="text" class="imageinput" name="settings[networks][<?= $row; ?>][icon]" value="<?= $network['icon']; ?>" data-thumb="<?= image($network['icon'] ? $network['icon'] : theme_url('image/no_image.png'), $thumb_width, $thumb_height); ?>"/>
						</span>
						<span class="social_url">
							<input type="text" name="settings[networks][<?= $row; ?>][href]" value="<?= $network['href']; ?>"/>
						</span>
						<img src="<?= theme_url('image/delete.png'); ?>" class="delete" onclick="$(this).closest('.social_network').remove()"/>
					</li>
				<? } ?>
			</ul>
		</td>
	</tr>
</table>

<script type="text/javascript">
	$('#social_network_list').ac_template('sn_list');

	$('.imageinput').ac_imageinput();

	$('#add_network').click(function () {
		var sn = $.ac_template('sn_list', 'add');
		sn.find('.imageinput').ac_imageinput();
	});

	$('#social_network_list').sortable();
</script>
