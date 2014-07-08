<div class="block-widget-view block">
	<div class="widget-view-list">
		<? foreach ($views as $row => $view) {
			$_GET = $view['query']; ?>

			<div class="widget-view <?= $view['show'] ? 'show' : ''; ?>" data-row="<?= $row; ?>" data-group="<?= $view['group']; ?>" data-query="<?= http_build_query($view['query']); ?>" data-view-id="<?= $view['view_id']; ?>">
				<div class="view-header clearfix">
					<h3 class="view-title" contenteditable><?= $view['title']; ?></h3>

					<div class="show-hide buttons">
						<a class="hide-view button" data-loading="Showing..."><?= _l("Hide"); ?></a>
						<a class="show-view button"><?= _l("Show"); ?></a>
					</div>

					<div class="configure">
						<?= build('select', array(
							'name'   => 'listing_id',
							'data'   => array('' => _l("(Select Listing)")) + $data_listings,
							'select' => $view['listing_id'],
							'key'    => false,
							'value'  => 'name',
						)); ?>
					</div>

					<div class="save-delete buttons">
						<a class="save-view button" data-loading="Saving..."><?= _l("Save"); ?></a>
						<a class="delete-view button remove" data-loading="Removing..."><?= _l("X"); ?></a>
					</div>
				</div>

				<div class="listing">
					<? if ($view['show']) { ?>
						<?= $view['controller']->$view['method'](); ?>
					<? } ?>
				</div>
			</div>
		<? } ?>
	</div>

	<div class="buttons views-actions">
		<a class="add-view button"><?= _l("New View"); ?></a>
	</div>
</div>

<script type="text/javascript">
	var listings = <?= json_encode($data_listings); ?>;

	$('.configure [name]').change(function () {
		var $this = $(this);
		var $view = $this.closest('.widget-view');
		$view.find('.show-view').click();
	});

	$('.save-view').click(function () {
		var $this = $(this);
		var $view = $this.closest('.widget-view');

		var query = $view.find('.refresh-listing').attr('href').replace(/^[^\?]*\?/, '');

		$view.attr('data-query', query);

		var data = {
			view_id: $view.attr('data-view-id'),
			group: $view.attr('data-group'),
			listing_id: $view.find('[name=listing_id]').val(),
			path: $view.find('[name=path]').val(),
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
		}, 'json');
	});

	$('.delete-view').click(function () {
		if (confirm("<?= _l("Are you sure you want to remove this view?"); ?>")) {
			var $this = $(this);
			var $view = $this.closest('.widget-view');

			$this.loading();

			$.post("<?= $remove_view; ?>", {view_id: $view.attr('data-view-id')}, function (response) {
				$view.remove();
			});
		}
	});

	$('.hide-view').click(function () {
		$(this).closest('.widget-view').removeClass('show');
	});

	$('.show-view').click(function () {
		var $view = $(this).closest('.widget-view').addClass('show');
		var $hide = $view.find('.hide-view');
		var listing_id = $view.find('[name=listing_id]').val();

		if (!listing_id) {
			return alert("<?= _l("Please Choose a listing first for this view"); ?>");
		}

		var listing = listings[listing_id];

		$hide.loading();

		$view.find('.widget-listing').addClass('loading');

		var q = $view.attr('data-query');
		query = (q ? q + '&' : '') + listing.query;

		$view.find('.listing').load(listing.path + (query ? '?' + query : ''), function () {
			$hide.loading('stop');
		});
	});

	$('.block-widget-view').ac_template('v-list', {defaults: <?= json_encode($views['__ac_template__']); ?>});

	$('.add-view').click(function () {
		var $vlist = $('.block-widget-view .widget-view-list').ac_template('v-list', 'add');
		$vlist.find('[name=listing_id] option:first').prop('selected', true);
		$vlist.find('.show-view').click();
	});
</script>