<div class="widget-listing">
	<? if ($show_messages) { ?>
		<?= $this->message->render(); ?>
	<? } ?>

	<? if ($show_limits) { ?>
		<div class="limits">
			<?= $this->sort->renderLimits($limit_settings); ?>
		</div>
	<? } ?>

	<a class="refresh-listing" href="<?= $refresh; ?>">Refresh</a>

	<? if (!empty($extra_cols)) { ?>
	<div class="extra-cols">
		<div class="label"><?= _l("Choose Columns"); ?></div>
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

	<div class="listings">
		<?= $listing; ?>
	</div>

	<? if ($show_pagination) { ?>
		<?= block('widget/pagination', null, $pagination_settings); ?>
	<? } ?>

	<script type="text/javascript">
		var $list_widget = $('.widget-listing').not('.activated').addClass('activated');

		$list_widget.find('.select-cols .scrollbox').sortable();

		$list_widget.find('.pagination a, .sortable, .filter-button, .reset-button, .limits a, .refresh-listing, .filter-cols')
			.click(function () {
				var $this = $(this);
				var $listing = $this.closest('.widget-listing');
				$listing.addClass("loading");

				var data = {columns: {}};

				$this.closest('.select-cols').find(':checked').each(function (i, e) {
					data.columns[$(e).val()] = i;
				});

				$.get($this.attr('href'), data, function (response) {
					$listing.siblings('.messages').remove();
					$listing.replaceWith(response);
				});

				return false;
			});
	</script>

</div>

