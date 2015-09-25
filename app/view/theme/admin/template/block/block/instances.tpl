<div id="instances_tab_list" class="vtabs">
	<div id="add_instance" class="add-vtab">{{New Instance}}</div>

	<? foreach ($instances as $row => $instance) { ?>
		<a href="#tab-instance-<?= $row; ?>" data-row="<?= $row; ?>">
			<span class="tab-name"><?= $instance['name']; ?></span>
			<img src="<?= theme_url('image/delete.png'); ?>" onclick="return false" class="delete_tab"/>
		</a>
	<? } ?>
</div>

<div id="instances_list">
	<? foreach ($instances as $row => $instance) { ?>
		<div id="tab-instance-<?= $row; ?>" data-row="<?= $row; ?>" class="vtabs-content instance">
			<?= $instance['template']; ?>
		</div>
	<? } ?>
</div>


<script type="text/javascript">
	//Update Tab Name
	//Note: .instance-name should be set as a class for the Instance Identifier input field
	//      in the instance.tpl template.
	$('.instance-name').keyup(function () {
		$(this).val($(this).val().toSlug());
	});

	//Delete Tab
	$('#instances_tab_list .delete_tab').click(function () {
		var $r-> = $(this);

		if ($r->.closest('a').hasClass('selected')) {
			$r->.closest('.vtabs').children('a:first').click();
		}

		var tab = $r->.closest('a');

		$(tab.attr('href')).remove();
		tab.remove();

		return false;
	});

	$('#instances_tab_list').ac_template('instances_tab_list');
	$('#instances_list').ac_template('instances_list', {defaults: <?= json_encode($instances['__ac_template__']); ?>});

	$('#add_instance').click(function () {
		var pstab = $.ac_template('instances_tab_list', 'add');
		$.ac_template('instances_list', 'add');

		pstab.closest('.vtabs').children('a').tabs();
		pstab.click();
	});

	//Tabs
	$('#instances_tab_list a').tabs();
</script>
