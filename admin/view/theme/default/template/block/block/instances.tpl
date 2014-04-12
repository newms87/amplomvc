<div id="instances_tab_list" class="vtabs">
	<div id="add_instance" class="add-vtab">
		<span><?= _l("New Instance"); ?></span>
		<img src="<?= URL_THEME_IMAGE . 'add.png'; ?>" alt=""/>
	</div>

	<? foreach ($instances as $row => $instance) { ?>
		<a href="#tab-instance-<?= $row; ?>" data-row="<?= $row; ?>">
			<span class="tab_name"><?= $instance['name']; ?></span>
			<img src="<?= URL_THEME_IMAGE . 'delete.png'; ?>" onclick="return false" class="delete_tab"/>
		</a>
	<? } ?>
</div>

<div id="instances_list">
	<? foreach ($instances as $row => $instance) { ?>
		<div id="tab-instance-<?= $row; ?>" data-row="<?= $row; ?>" class="vtabs-content instance">
			<table class="form">
				<tr>
					<td><?= _l("Instance Name"); ?></td>
					<td>
						<input type="text" class="tab_name instance_name" name="instances[<?= $row; ?>][name]" pattern="[a-z-_0-9]" value="<?= $instance['name']; ?>"/>
					</td>
				</tr>
				<tr>
					<td><?= _l("Show Block Title?"); ?></td>
					<td><?= $this->builder->build('radio', $data_yes_no, "instances[$row][show_title]", $instance['show_title']); ?></td>
				</tr>
			</table>
		</div>
	<? } ?>

</div>


<script type="text/javascript">
	//Update Tab Name
	$('.instance_name').keyup(function () {
		update_instance_select();
	});

	//Delete Tab
	$('#instances_tab_list .delete_tab').click(function () {
		if ($(this).closest('a').hasClass('selected')) {
			$(this).closest('.vtabs').children('a:first').click();
		}

		var tab = $(this).closest('a');

		$(tab.attr('href')).remove();
		tab.remove();

		update_instance_select();

		return false;
	});

	//Profile Settings
	$('#instances_tab_list').ac_template('instances_tab_list');
	$('#instances_list').ac_template('instances_list', {defaults: <?= json_encode($instances['__ac_template__']); ?>});

	$('#add_instance').click(function () {
		var pstab = $.ac_template('instances_tab_list', 'add');
		$.ac_template('instances_list', 'add');

		pstab.closest('.vtabs').children('a').tabs();
		pstab.click();

		update_instance_select();
	});

	function update_instance_select() {
		var profile_template = $.ac_template.templates['profile_list'].template.find('select[name*=instance_id]');
		context = $('select[name*=instance_id]').add(profile_template);

		var options = '';

		$('#instances_tab_list a').each(function (i, e) {
			options += '<option value="' + $(e).attr('data-row') + '">' + $(e).find('.tab_name').html() + '</option>';
		});

		context.html(options);
	}

	//Tabs
	$('#instances_tab_list a').tabs();
</script>
