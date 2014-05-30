<div class="block-widget-view block">
	<? foreach ($views as $row => $view) {
		$_GET = $view['query']; ?>

		<div class="widget-view <?= $view['show'] ? 'show' : ''; ?>" data-row="<?= $row; ?>" data-path="<?= $view['path']; ?>" data-query="<?= http_build_query($view['query']); ?>" data-view-id="<?= $view['view_id']; ?>">
			<div class="view-header clearfix">
				<h3 class="view-title" contenteditable><?= $view['title']; ?></h3>
				<div class="showing buttons">
					<a class="hide-view button" data-loading="Showing..."><?= _l("Hide"); ?></a>
					<a class="show-view button"><?= _l("Show"); ?></a>
				</div>

				<div class="save-delete buttons">
					<a class="save-view button" data-loading="Saving..."><?= _l("Save View"); ?></a>
					<a class="delete-view button" data-loading="Removing..."><?= _l("X"); ?></a>
				</div>
			</div>

			<div class="listing">
				<? if ($view['show']) { ?>
					<?= $controller->$listing(); ?>
				<? } ?>
			</div>
		</div>
	<? } ?>

	<a class="add-view button"><?= _l("New View"); ?></a>
</div>

<script type="text/javascript">
	$('.save-view').click(function () {
		var $this = $(this);
		var $view = $this.closest('.widget-view');

		var query = $view.find('.refresh-listing').attr('href').replace(/^[^\?]*\?/, '');

		var data = {
			view_id: $view.attr('data-view-id'),
			path: $view.attr('data-path'),
			query: query,
			title: $view.find('.view-title').html(),
			show: $view.hasClass("show") ? 1 : 0
		}

		$this.loading();

		$.post("<?= $save_view;?>", data, function (response) {
			$this.loading('stop');
			if (response.view_id) {
				$view.attr('data-view-id', response.view_id);
				response.view_id = null;
			}

			$view.ac_msg(response);
		},'json');
	});

	$('.delete-view').click(function() {
		if (confirm("<?= _l("Are you sure you want to remove this view?"); ?>")) {
			var $this = $(this);
			var $view = $this.closest('.widget-view');

			$this.loading();

			$.post("<?= $remove_view; ?>", {view_id: $view.attr('data-view-id')}, function(response) {
				$view.remove();
			});
		}
	});

	$('.hide-view').click(function() {
		$(this).closest('.widget-view').removeClass('show');
	});

	$('.show-view').click(function() {
		var $view = $(this).closest('.widget-view').addClass('show');
		var $hide = $view.find('.hide-view');

		$hide.loading();

		$view.find('.widget-listing').addClass('loading');

		$view.find('.listing').load($view.attr('data-path') + '?' + $view.attr('data-query'), function() {
			$hide.loading('stop');
		});
	});

	$('.block-widget-view').ac_template('v-list', {defaults: <?= json_encode($views['__ac_template__']); ?>});

	$('.add-view').click(function() {
		var $vlist = $('.block-widget-view').ac_template('v-list', 'add');
		$vlist.find('.show-view').click();
	});
</script>