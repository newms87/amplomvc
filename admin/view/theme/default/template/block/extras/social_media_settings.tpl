<table class="form">
	<tr>
		<td>
			<?= $entry_social_networks; ?>
			<div id="add_network" onclick="add_network()"><?= $button_add_network; ?></div>
		</td>
		<td>
			<ul id="social_network_list">
				<? $network_id = 0; ?>
				<? foreach ($networks as $network) { ?>
					<li class="social_network">
					<span class="social_icon">
						<?= $this->builder->set_builder_template('click_image_small'); ?>
						<?= $this->builder->image_input("settings[networks][$network_id][icon]", $network['icon'], $network['thumb'], $no_image, $thumb_width, $thumb_height); ?>
					</span>
					<span class="social_url">
						<input type="text" name="settings[networks][<?= $network_id; ?>][href]" value="<?= $network['href']; ?>"/>
					</span>
						<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" class='delete' onclick="$(this).parent().remove()"/>
					</li>
					<? $network_id++; ?>
				<? } ?>
			</ul>
		</td>
	</tr>
</table>

<ul id="network_template" style="display:none">
	<li class="social_network">
		<span class="social_icon">
			<?= $this->builder->set_builder_template('click_image_small'); ?>
			<?= $this->builder->image_input("settings[networks][%net_id%][icon]", null, null, $no_image, $thumb_width, $thumb_height); ?>
		</span>
		<span class="social_url">
			<input type="text" name="settings[networks][%net_id%][href]" value="http://www.your-network.com"/>
		</span>
		<img src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" class='delete' onclick="$(this).parent().remove()"/>
	</li>
</ul>

<script type="text/javascript">//<!--
	var network_id = <?= $network_id+1; ?>;

	function add_network(data) {
		html = $($('#network_template').html().replace(/%net_id%/g, network_id));

		img_input = html.find('.image input');
		img_thumb = html.find('.image img');

		//we update the ID tag because the image uploader will not work properly if there are duplicates.
		img_input.attr('id', img_input.attr('id') + network_id);
		img_thumb.attr('id', img_thumb.attr('id') + network_id);

		$('#social_network_list').append(html);

		network_id++;
	}

	$('#social_network_list').sortable();

	$('#form').on('saving', function () {
		$('#network_template').remove();
	});
//--></script>