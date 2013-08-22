<div class="box-search">
	<form method='post' action='<?= $results_url; ?>' id='search_form' target='search_results'>
		<input type='hidden' name='action' value='betty_search'/>
		<ul>
			<li>
				<?=
				$this->builder->build_custom_select_dropdown($categories, 'search_category', array(
				                                                                                  'display_name' => 'CATEGORY',
				                                                                                  'value'        => ""
				                                                                             ), $search_category); ?>
			</li>
			<li>
				<?
				$color_list = array();
				foreach ($colors as $key => $c) {
					$color_style  = "style='background: " . get_color_hex($c) . "'";
					$color_list[] = array(
						'display_name' => $c,
						'value'        => $key,
						'before'       => "<div class='color_circle' $color_style></div>"
					);
				}?>
				<?=
				$this->builder->build_custom_select_dropdown($color_list, 'search_color', array(
				                                                                               'display_name' => 'COLOR',
				                                                                               'value'        => ""
				                                                                          ), $search_color); ?>
			</li>
			<li>
				<?=
				$this->builder->build_custom_select_dropdown($styles, 'search_style', array(
				                                                                           'display_name' => 'STYLE',
				                                                                           'value'        => ""
				                                                                      ), $search_style); ?>
			</li>
			<li>
				<?=
				$this->builder->build_custom_select_dropdown($countries, 'search_country', array(
				                                                                                'display_name' => 'COUNTRY',
				                                                                                'value'        => ""
				                                                                           ), $search_country); ?>
			</li>
		</ul>
		<input type='submit' value='Go' class='search_go'/>

		<div style='clear:both'></div>
	</form>
	<form method='post' action='<?= $results_url; ?>' target='search_results'>
		<input type='hidden' name='action' value='betty_search'/>
		<input id='gen_search' type='text' value='<?= empty($search_general) ? "SEARCH HERE" : $search_general; ?>' name='search_general'/>
		<input id='gen_search_go' type='submit' value='Go' class='search_go'/>

		<div style='clear:both'></div>
	</form>

	<div name='search_results' id='search_results'></div>

	<script type='text/javascript'>
		// <!--
		function toggleShowSearch(event, args) {
			if (event.preventDefault)event.preventDefault();
			event.returnValue = false;

			var sr = $('#search_results').css('z-index', 5001);
			var show = args ? args.show || false : false;

			if (show === sr.data('showing') || sr.is(':animated'))return;

			if (typeof sr.data('width') == 'undefined' || sr.data('width') === null)
				sr.data('width', sr.width());
			if (show) {
				$.post('<?= $results_url; ?>', args.form.serialize(), function (data) {
					$('#search_results').html(data);
				});
				sr.css({width: 0}).show().animate({width: sr.data('width')}, {duration: 500, complete: function () {
					sr.data('showing', true);
					$(this).children('#search_content').fadeIn(500);
				}});
				var close = $("<div id='search_close' style='width: 2000px; height:2000px; background: rgba(0,0,0,.2);z-index:5000;position:absolute;top:0;left:0;'></div>");
				close.click(toggleShowSearch);
				$('body').append(close);
			}
			else {
				sr.animate({width: 0}, {duration: 500, complete: function () {
					sr.data('showing', false);
				}}).hide();
				$('#search_close').remove();
				$('#search_results').html('');
			}
			event.stopImmediatePropagation();
		}

		$(document).ready(function () {
			//General search input
			$('#gen_search').focus(function () {
				if ($(this).val() == 'SEARCH HERE')$(this).addClass('active').val('');
			});
			$('#gen_search').blur(function () {
				if ($(this).val() == '') {
					$(this).removeClass('active').val('SEARCH HERE');
				}
			});

			//Search Buttons
			$('.search_go').click(function (event) {
				toggleShowSearch(event, {show: true, form: $(this).closest('form')});
			});

			$('#search_results').mousewheel(function (e, d) {
				scroll_max = this.scrollHeight - $('#search_results').height();
				if ((this.scrollTop >= scroll_max && d < 0) || (this.scrollTop <= 0 && d > 0) || scroll_max < 1) {
					e.preventDefault();
				}
				;
			});
		});
		//	-->
	</script>
</div>
