<div class="block-widget-view block">
	<div class="widget-view-list">
		<? foreach ($views as $row => $view) {
			$_REQUEST = $view['query'];
			$_GET = $view['query']; ?>

			<div class="widget-view <?= $view['show'] ? 'show' : ''; ?>" data-row="<?= $row; ?>" data-group="<?= $view['group']; ?>" data-query="<?= http_build_query($view['query']); ?>" data-view-id="<?= $view['view_id']; ?>">
				<div class="view-header clearfix">
					<h3 class="view-title" <?= $can_modify ? 'contenteditable' : ''; ?>><?= $view['title']; ?></h3>

					<div class="show-hide buttons">
						<a class="hide-view button" data-loading="Showing..."><?= _l("Hide"); ?></a>
						<a class="show-view button"><?= _l("Show"); ?></a>
					</div>

					<? if ($can_modify) { ?>
						<div class="configure">
							<div class="choose-view-box">
								<?=
								build('select', array(
									'name'   => 'view_listing_id',
									'data'   => array('' => _l("(Select Listing)")) + $data_view_listings,
									'select' => $view['view_listing_id'],
									'key'    => false,
									'value'  => 'name',
								)); ?>
							</div>
						</div>

						<div class="save-delete buttons">
							<a class="move-up button move"><b class="move-up sprite"></b></a>
							<a class="move-down button move"><b class="sprite move-down"></b></a>
							<a class="save-view button" data-loading="Saving..."><?= _l("Save"); ?></a>
							<a class="delete-view button remove" data-loading="Removing..."><?= _l("X"); ?></a>
						</div>
					<? } ?>
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
		<a class="create-view button"><?= _l("Create View"); ?></a>

		<div class="create-view-box">
			<form action="<?= site_url('block/widget/views/create', array('redirect' => $this->url->here())); ?>" method="post">
				<div class="description"><?= _l("Provide your own SELECT SQL Statement. The view will be created as a filterable / sortable table."); ?></div>
				<input type="hidden" name="group" value="<?= $group; ?>" />
				<input type="text" name="name" value="<?= "View Name"; ?>"/>
				<br/>
				<textarea name="sql" placeholder="<?= _l("WHERE Status = 'Complete'"); ?>"></textarea>
				<button class="submit-view"><?= _l("Create View"); ?></button>
			</form>

			<a class="button close">X</a>
		</div>
	</div>

</div>

<script type="text/javascript">
	var listings = <?= json_encode($data_view_listings); ?>;

	$('.widget-view-list').find('.button.move').click(function() {
		var $this = $(this);
		var $view = $this.closest('.widget-view');
		if ($this.hasClass('move-down')) {
			$view.next().after($view);
		} else {
			$view.prev().before($view);
		}

		var sort_order = {};

		$('.widget-view-list .widget-view').each(function (i,e) {
			sort_order[$(e).attr('data-view-id')] = i;
		});

		$.post("<?= site_url('block/widget/views/save_sort_order'); ?>", {'sort_order': sort_order}, function (response) {
			if (!response.success) {
				$('.widget-view-list').ac_msg(response);
			}
		}, 'json');
	});

	$('.create-view').click(function() {
		$('.create-view-box').addClass('show');
	});

	$('.create-view-box .close').click(function() {
		$(this).closest('.create-view-box').removeClass('show');
	});

	$('.hide-view').click(function () {
		$(this).closest('.widget-view').removeClass('show');
	});

	$('.show-view').click(function () {
		var $view = $(this).closest('.widget-view').addClass('show');
		var $hide = $view.find('.hide-view');
		var view_listing_id = $view.find('[name=view_listing_id]').val();

		if (!view_listing_id) {
			return alert("<?= _l("Please Choose a listing first for this view"); ?>");
		}

		var listing = listings[view_listing_id];

		$hide.loading();

		$view.find('.widget-listing').addClass('loading');

		var q = $view.attr('data-query');
		query = (q ? q + '&' : '') + listing.query;

		$view.find('.listing').load(listing.path + (query ? '?' + query : ''), function () {
			$hide.loading('stop');
		});
	});

	<? if ($can_modify) { ?>
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
			view_listing_id: $view.find('[name=view_listing_id]').val(),
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
		var $this = $(this);
		if (!$this.hasClass('confirm')) {
			setTimeout(function () {
				$this.removeClass('confirm').loading('stop');
			}, 2000);
			return $this.loading({text: "<?= _l("Confirm Delete"); ?>"}).addClass('confirm');
		}

		if (confirm("<?= _l("Are you sure you want to remove this view?"); ?>")) {
			var $this = $(this);
			var $view = $this.closest('.widget-view');

			$this.loading();

			$.post("<?= $remove_view; ?>", {view_id: $view.attr('data-view-id')}, function (response) {
				$view.remove();
			});
		}
	});
	<? } ?>

	$('.block-widget-view').ac_template('v-list', {defaults: <?= json_encode($views['__ac_template__']); ?>});

	$('.add-view').click(function () {
		var $vlist = $('.block-widget-view .widget-view-list').ac_template('v-list', 'add');
		if ($vlist.find('[name=view_listing_id]').val()) {
			$vlist.find('.show-view').click();
		}
	});
</script>