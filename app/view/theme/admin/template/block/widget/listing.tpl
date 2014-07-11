<div class="widget-listing">
	<? if ($show_messages) { ?>
		<?= $this->message->render(); ?>
	<? } ?>

	<? if ($show_limits) { ?>
		<div class="limits">
			<?= $this->sort->renderLimits($limit_settings); ?>
		</div>
	<? } ?>

	<div class="view-controls">
		<a class="refresh-listing" href="<?= $refresh; ?>">Refresh</a>

		<button class="modify-view"><?= _l("Modify View"); ?></button>

		<div class="view-config">
			<button class="close">X</button>

			<div class="view-tabs htabs">
				<a href=".col-tab"><?= _l("Columns"); ?></a>
				<a href=".group-tab"><?= _l("Groups / Aggregate"); ?></a>
				<a href=".sql-tab"><?= _l("Custom View"); ?></a>
			</div>

			<? if (!empty($extra_cols)) { ?>
			<div class="col-tab tab-content">
				<div class="select-cols">
					<?=
					build('multiselect', array(
						'name'   => 'columns',
						'data'   => $extra_cols,
						'select' => array_keys($columns),
						'key'    => 'Field',
						'value'  => 'display_name',
					)); ?>

					<div class="buttons">
						<a class="filter-cols button" href="<?= site_url($listing_path, $this->url->getQueryExclude('columns')); ?>"><?= _l("Apply"); ?></a>
					</div>
				</div>
			</div>
			<? } ?>

			<div class="group-tab tab-content">
				Group By / Aggregate... Waiting to be implemented.
			</div>

			<? //TODO: Move this to the Views Page, this should be in replacement of the listing_id dropdown ?>
			<div class="sql-tab tab-content">
				<form action="<?= site_url('block/widget/view/create', array('redirect' => $this->url->here())); ?>" method="post">
					<div class="description"><?= _l("Provide your own SELECT SQL Statement. The view will be created as a filterable / sortable table."); ?></div>
					<input type="text" name="name" value="<?= "View Name"; ?>" /><br />
					<textarea name="view_sql" placeholder="<?= _l("WHERE Status = 'Complete'"); ?>"></textarea>
					<button class="submit-view-sql"><?= _l("Create View"); ?></button>
				</form>
			</div>
		</div>
	</div>

	<div class="listings">
		<?= $listing; ?>
	</div>

	<? if ($show_pagination) { ?>
		<?= block('widget/pagination', null, $pagination_settings); ?>
	<? } ?>

	<script type="text/javascript">
		var $list_widget = $('.widget-listing').not('.activated').addClass('activated');

		$list_widget.find('.view-tabs a').tabs();

		$list_widget.find('.modify-view').click(function() {
			$(this).siblings('.view-config').toggleClass('show');
		});

		$list_widget.find('.view-config .close').click(function (){
			$(this).closest('.view-config').removeClass('show');
		});

		$list_widget.find('.select-cols .scrollbox').sortable();

		$list_widget.find('.pagination a, .sortable, .filter-button, .reset-button, .limits a, .refresh-listing, .filter-cols')
			.click(function () {
				var $this = $(this);

				if (!$this.attr('href')) {
					return false;
				}

				var $listing = $this.closest('.widget-listing');
				$listing.addClass("loading");

				var data = {columns: {}};

				$this.closest('.select-cols').find(':checked').each(function (i, e) {
					data.columns[$(e).val()] = i;
				});

				$.get($this.attr('href'), data, function (response) {
					var $parent = $listing.closest('.listing');
					$listing.siblings('.messages').remove();
					$listing.replaceWith(response);
					init_ajax();
					$parent.trigger('loaded');
				});

				return false;
			});
	</script>

</div>

