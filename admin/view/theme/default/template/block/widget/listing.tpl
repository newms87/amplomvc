<div id="listing">
	<? if ($show_messages) { ?>
		<?= $this->message->render(); ?>
	<? } ?>

	<? if ($show_limits) { ?>
		<div class="limits">
			<?= $this->sort->renderLimits($limit_settings); ?>
		</div>
	<? } ?>

	<div class="listings">
		<?= $listing; ?>
	</div>

	<? if ($show_pagination) { ?>
		<?= block('widget/pagination', null, $pagination_settings); ?>
	<? } ?>

	<? if (!empty($ajax)) { ?>
		<script type="text/javascript">
			$('#listing').find('.pagination a, .sortable, .filter-button, .reset-button, .limits a').click(function(){
				var $this = $(this);
				var $listing = $('#listing');
				$listing.addClass("loading");

				$.get($this.attr('href'),{}, function(response) {
					//This is necessary for batch action to be compatible with search / filter
					if (history.pushState) {
						var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + $this.attr('href').replace(/^[^?]*\?/,'').replace(/&ajax=?\d/,'');
						window.history.pushState({path:newurl},'',newurl);
					}

					$listing.siblings('.messages').remove();
					$listing.replaceWith(response);
				});

				return false;
			});
		</script>
	<? } ?>
</div>

