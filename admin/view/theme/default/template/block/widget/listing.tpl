<div id="listing">
	<div class="listings">
		<?= $listing; ?>
	</div>

	<?php if (!empty($pagination)) { ?>
		<?= _block('widget/pagination', null, $pagination_settings); ?>
	<?php } ?>
</div>


<?php if (!empty($ajax)) { ?>
	<script type="text/javascript">
		$('#listing').find('.pagination a, .sortable, .filter-button, .reset-button').click(function(){
			var $this = $(this);
			$('#listing').addClass("loading");

			$.get($this.attr('href'),{}, function(response) {
				$('#listing').replaceWith(response);

				//This is necessary for batch action to be compatible with search / filter
				if (history.pushState) {
					var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + $this.attr('href').replace(/^[^?]*\?/,'');
					window.history.pushState({path:newurl},'',newurl);
				}
			});

			return false;
		});
	</script>
<?php } ?>
