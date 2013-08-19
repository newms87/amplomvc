<?= $header; ?>
	<div class="content">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'user.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a onclick="submit_navigation_form()" class="button"><?= $button_save; ?></a>
					<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
				</div>
			</div>
			<div class="content">
				<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_name; ?></td>
							<td><input type="text" name="name" value="<?= $name; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_store; ?></td>
							<? $this->builder->set_config('store_id', 'name'); ?>
							<td><?= $this->builder->build('multiselect', $data_stores, "stores", $stores); ?></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><?= $this->builder->build('select', $data_statuses, 'status', (int)$status); ?></td>
						</tr>

						<tr id="links_list_data">
							<td colspan="2">
								<div id='links_list' class='box'>
									<div class='heading'><h3><?= $entry_links; ?></h3></div>

									<div class="content">
										<div class="left">
											<div id='new_navigation_link'>
												<div class="editable">
													<input type="hidden" class="parent_id" name="links[%link_num%][parent_id]"
													       value="0"/>

													<div class="link_entry_display_name">
														<label
															for="link_display_name_%link_num%"><?= $entry_link_display_name; ?></label>
														<input id='link_display_name_%link_num%' type="text"
														       name="links[%link_num%][display_name]"
														       onkeyup="update_display_name($(this));" value=""/>
													</div>
													<div class="link_entry_name">
														<label for="link_name_%link_num%"><?= $entry_link_name; ?></label>
														<input id='link_name_%link_num%' type="text" name="links[%link_num%][name]"
														       value=""/>
													</div>
													<div class="link_entry_title">
														<label for="link_title_%link_num%"><?= $entry_link_title; ?></label>
														<input id='link_title_%link_num%' type="text" name="links[%link_num%][title]"
														       value=""/>
													</div>
													<div class="link_entry_href">
														<label for="link_href_%link_num%"><?= $entry_link_href; ?></label>
														<input id='link_href_%link_num%' type="text" class='long'
														       name="links[%link_num%][href]" value=""/>
													</div>
													<div class="link_entry_query">
														<label for="link_query_%link_num%"><?= $entry_link_query; ?></label>
														<input id='link_query_%link_num%' type="text" class='long'
														       name="links[%link_num%][query]" value=""/>
													</div>
													<div class="link_entry_is_route">
														<label for="link_is_route_%link_num%"><?= $entry_link_is_route; ?></label>
														<?= $this->builder->build('select', $data_yes_no, "links[%link_num%][is_route]", 0, array('id' => "link_is_route_%link_num%")); ?>
													</div>
													<div class="link_entry_status">
														<label for="link_status_%link_num%"><?= $entry_link_status; ?></label>
														<?= $this->builder->build('select', $data_statuses, "links[%link_num%][status]", 1, array('id' => "link_status_%link_num%")); ?>
													</div>
												</div>

												<input type="button" class="button" onclick="add_navigation_link();"
												       value="<?= $text_add_link; ?>"/>
											</div>
										</div>
										<div class="right">
											<ul id='sorted_links'>
												<? $max_link = 0; ?>
												<? foreach ($links as $link_num => $link) { ?>
													<? if (isset($link['navigation_id'])) {
														$link_num = $link['navigation_id'];
													} ?>
													<? $max_link = max($max_link, $link_num); ?>
													<li link_id='<?= $link_num; ?>' parent_id="<?= $link['parent_id']; ?>">
														<div class="link_info">
															<div class="link_name"><span
																	class="display_name"><?= $link['display_name']; ?></span><span
																	class="show_link_edit" onclick="toggle_edit_link($(this))"><span
																		class="edit_text"><?= $text_edit_link; ?></span><img
																		class="remove_link" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>"
																		onclick="$(this).closest('li').remove();"/></span></div>
															<div class="editable" style="display:none">
																<input type="hidden" class="parent_id"
																       name="links[<?= $link_num; ?>][parent_id]" value="0"/>

																<div class="link_entry_display_name">
																	<label
																		for="link_display_name_<?= $link_num; ?>"><?= $entry_link_display_name; ?></label>
																	<input id='link_display_name_<?= $link_num; ?>' type="text"
																	       name="links[<?= $link_num; ?>][display_name]"
																	       onkeyup="update_display_name($(this));"
																	       value="<?= $link['display_name']; ?>"/>
																</div>
																<div class="link_entry_name">
																	<label
																		for="link_name_<?= $link_num; ?>"><?= $entry_link_name; ?></label>
																	<input id='link_name_<?= $link_num; ?>' type="text"
																	       name="links[<?= $link_num; ?>][name]"
																	       value="<?= $link['name']; ?>"/>
																</div>
																<div class="link_entry_title">
																	<label
																		for="link_title_<?= $link_num; ?>"><?= $entry_link_title; ?></label>
																	<input id='link_title_<?= $link_num; ?>' type="text"
																	       name="links[<?= $link_num; ?>][title]"
																	       value="<?= $link['title']; ?>"/>
																</div>
																<div class="link_entry_href">
																	<label
																		for="link_href_<?= $link_num; ?>"><?= $entry_link_href; ?></label>
																	<input id='link_href_<?= $link_num; ?>' type="text"
																	       name="links[<?= $link_num; ?>][href]"
																	       value="<?= $link['href']; ?>"/>
																</div>
																<div class="link_entry_query">
																	<label
																		for="link_query_<?= $link_num; ?>"><?= $entry_link_query; ?></label>
																	<input id='link_query_<?= $link_num; ?>' type="text"
																	       name="links[<?= $link_num; ?>][query]"
																	       value="<?= $link['query']; ?>"/>
																</div>
																<div class="link_entry_is_route">
																	<label
																		for="link_is_route_<?= $link_num; ?>"><?= $entry_link_is_route; ?></label>
																	<?= $this->builder->build('select', $data_yes_no, "links[$link_num][is_route]", $link['is_route'], array('id' => "link_is_route_$link_num")); ?>
																</div>
																<div class="link_entry_status">
																	<label
																		for="link_status_<?= $link_num; ?>"><?= $entry_link_status; ?></label>
																	<?= $this->builder->build('select', $data_statuses, "links[$link_num][status]", $link['status'], array('id' => "link_status_$link_num")); ?>
																</div>
															</div>
														</div>

														<ul></ul>
													</li>
												<? } ?>
												<? $max_link++; ?>
											</ul>
										</div>
									</div>
								</div>
							</td>
						</tr>
					</table>
			</div>
			</form>
		</div>
	</div>
	</div>
<?= $footer; ?>

	<script type="text/javascript">//<!--
		$(document).ready(function () {
			make_navigation_sortable();
		});

		function make_navigation_sortable() {
			list = $('#links_list ul');

			list.find('[parent_id]').each(function (i, e) {
				$(e).appendTo($('[link_id=' + $(e).attr('parent_id') + '] > ul'));
			});

			list.sortable({
				delay: 100,
				connectWith: "#links_list ul",
				placeholder: 'placeholder',
				change: show_placeholder
			});
		}

		function show_placeholder(event, ui) {
			next = $('#sorted_links .placeholder').next();
			prev = $('#sorted_links .placeholder').prev();

			if (prev.hasClass('ui-sortable-helper')) {
				prev = prev.prev();
			}

			if ((next.length && next.hasClass('old')) || (prev.length && prev.hasClass("old"))) {
				return;
			}

			$('#sorted_links .old').removeClass('old');
			$('.show_empty').removeClass("show_empty");

			next.addClass('old');
			prev.addClass('old');

			if (next.length) {
				next.children('ul:empty').addClass('show_empty');
			}

			if (prev.length) {
				prev.children('ul:empty').addClass('show_empty');
			}
		}

		$('#links_list').on('sortstart', function (event, ui) {
			//$('#sorted_links > li > ul').hide();
		});

		$('#links_list').on('sortstop', function (event, ui) {
			ui.item.children('ul').show();
			$('#sorted_links .old').removeClass('old');
			$('#sorted_links .show_empty').removeClass("show_empty");
		});

		function submit_navigation_form() {
			build_link_tree($('#sorted_links'), 0);

			$('#form').submit();
		}

		function build_link_tree(ul, parent_id) {
			if (!ul.children().length) return;

			ul.children().each(function (i, e) {
				$(e).children('.link_info').children('.editable').children('.parent_id').val(parent_id);
				build_link_tree($(e).children('ul'), $(e).attr('link_id'));
			});
		}

		function toggle_edit_link(context) {
			edit = context.parent().siblings('.editable');

			if (edit.hasClass('showing')) {
				edit.slideUp().removeClass('showing');
				context.find('.edit_text').html('<?= $text_edit_link; ?>');
			}
			else {
				edit.slideDown().addClass('showing');
				context.find('.edit_text').html('<?= $text_hide_link; ?>');
			}
		}

		var link_num = <?= $max_link; ?>;
		function add_navigation_link() {
			var link_info = $('#new_navigation_link .editable').clone(true);

			display_name = link_info.find('input[name="links[%link_num%][display_name]"]').val();

			attrs = $('#new_navigation_link .editable [name^="links"]');
			var attr_save = {};

			attrs.each(function (i, e) {
				attr_save[$(e).attr('name').replace(/%link_num%/g, link_num)] = $(e).val();
			});

			link_info.html(link_info.html().replace(/%link_num%/g, link_num));

			for (var a in attr_save) {
				link_info.find('[name="' + a + '"]').val(attr_save[a]);
			}

			html = '<li link_id="%link_num%">';
			html += '	<div class="link_info">';
			html += '		<div class="link_name"><span class="display_name">' + display_name + '</span><span class="show_link_edit" onclick="toggle_edit_link($(this))"><span class="edit_text"><?= $text_edit_link; ?></span><img class="remove_link" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" onclick="$(this).closest(\'li\');"/></span></div>';
			html += '	</div>';
			html += '<ul></ul>';
			html += '</li>';

			var new_link = $(html.replace(/%link_num%/g, link_num));

			new_link.find('ul').sortable({
				connectWith: "#links_list ul",
				placeholder: "ui-state-highlight"
			});

			new_link.find('.link_info').append(link_info.hide());

			$('#sorted_links').append(new_link);

			$('#new_navigation_link .editable').find('input').val('');

			link_num++;
		}

		function update_display_name(context) {
			context.closest('.link_info').find('.display_name').html(context.val())
		}
//--></script>
<?= $this->builder->js('errors', $errors); ?>