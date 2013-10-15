<?= $header; ?>
	<div class="section">
		<?= $this->breadcrumb->render(); ?>
		<div class="box">
			<div class="heading">
				<h1><img src="<?= HTTP_THEME_IMAGE . 'user.png'; ?>" alt=""/> <?= $head_title; ?></h1>

				<div class="buttons">
					<a onclick="$('#form').submit()" class="button"><?= $button_save; ?></a>
					<a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a>
				</div>
			</div>
			<div class="section">
				<form action="<?= $save; ?>" method="post" enctype="multipart/form-data" id="form">
					<table class="form">
						<tr>
							<td class="required"> <?= $entry_name; ?></td>
							<td><input type="text" name="name" value="<?= $name; ?>"/></td>
						</tr>
						<tr>
							<td class="required"> <?= $entry_store; ?></td>
							<? $this->builder->setConfig('store_id', 'name'); ?>
							<td><?= $this->builder->build('multiselect', $data_stores, "stores", $stores); ?></td>
						</tr>
						<tr>
							<td><?= $entry_status; ?></td>
							<td><?= $this->builder->build('select', $data_statuses, 'status', (int)$status); ?></td>
						</tr>

						<tr id="links_list_data">
							<td colspan="2">
								<div id="links_list" class="box">
									<div class="heading"><h3><?= $entry_links; ?></h3></div>

									<div class="section">
										<div class="left">
											<a onclick="add_navigation_link()" class="button"><?= $text_add_link; ?></a>
										</div>

										<div class="right">
											<ul id="sorted_links">
												<? foreach ($links as $nav_id => $link) { ?>
													<li class="nav_link" data-row="<?= $nav_id; ?>" data-parent_id="<?= $link['parent_id']; ?>">
														<div class="link_info">
															<div class="link_name">
																<span class="display_name"><?= $link['display_name']; ?></span>
																<span class="show_link_edit" onclick="toggle_edit_link($(this))">
																<span class="edit_text"><?= $text_edit_link; ?></span>
																<img class="remove_link" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" onclick="$(this).closest('li').remove();"/></span>
															</div>
															<div class="editable" style="display:none">
																<input type="hidden" class="parent_id" name="links[<?= $nav_id; ?>][parent_id]" value="<?= $link['parent_id']; ?>"/>

																<div class="link_entry_display_name">
																	<label for="link_display_name_<?= $nav_id; ?>"><?= $entry_link_display_name; ?></label>
																	<input id="link_display_name_<?= $nav_id; ?>" type="text" name="links[<?= $nav_id; ?>][display_name]" onkeyup="update_display_name($(this));" value="<?= $link['display_name']; ?>"/>
																</div>

																<div class="link_entry_name">
																	<label for="link_name_<?= $nav_id; ?>"><?= $entry_link_name; ?></label>
																	<input id="link_name_<?= $nav_id; ?>" type="text" name="links[<?= $nav_id; ?>][name]" value="<?= $link['name']; ?>"/>
																</div>
																<div class="link_entry_title">
																	<label for="link_title_<?= $nav_id; ?>"><?= $entry_link_title; ?></label>
																	<input id="link_title_<?= $nav_id; ?>" type="text" name="links[<?= $nav_id; ?>][title]" value="<?= $link['title']; ?>"/>
																</div>
																<div class="link_entry_href">
																	<label for="link_href_<?= $nav_id; ?>"><?= $entry_link_href; ?></label>
																	<input id="link_href_<?= $nav_id; ?>" type="text" name="links[<?= $nav_id; ?>][href]" value="<?= $link['href']; ?>"/>
																</div>
																<div class="link_entry_query">
																	<label for="link_query_<?= $nav_id; ?>"><?= $entry_link_query; ?></label>
																	<input id="link_query_<?= $nav_id; ?>" type="text" name="links[<?= $nav_id; ?>][query]" value="<?= $link['query']; ?>"/>
																</div>
																<div class="link_entry_condition">
																	<label for="link_condition_<?= $nav_id; ?>"><?= $entry_link_condition; ?></label>
																	<?= $this->builder->build('select', $data_conditions, "links[$nav_id][condition]", $link['condition'], array('id' => "link_condition_$nav_id")); ?>
																</div>
																<div class="link_entry_status">
																	<label for="link_status_<?= $nav_id; ?>"><?= $entry_link_status; ?></label>
																	<?= $this->builder->build('select', $data_statuses, "links[$nav_id][status]", $link['status'], array('id' => "link_status_$nav_id")); ?>
																</div>
															</div>
														</div>

														<ul class="child_list"></ul>
													</li>
												<? } ?>
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
	$('#sorted_links').ac_template('link_list', {defaults: <?= json_encode($links['__ac_template__']); ?>});

	function add_navigation_link() {
		var new_link = $.ac_template('link_list', 'add');

		new_link.find('ul').sortable({
			delay: 100,
			connectWith: "#links_list ul",
			placeholder: "ui-state-highlight",
			change: show_placeholder
		});
	}

	$('.nav_link').each(function(i,e){
		var parent_id = $(e).children('.link_info').find('.parent_id').val();
		if (parent_id > 0) {
			$('.nav_link[data-row=' + parent_id + ']').children('.child_list').append($(e));
		}
	});

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

	$('#links_list').on('sortstop', function (event, ui) {
		ui.item.children('ul').show();
		$('#sorted_links .old').removeClass('old');
		$('#sorted_links .show_empty').removeClass("show_empty");

		var parent = ui.item.parent().closest('li.nav_link');
		ui.item.children('.link_info').find('.parent_id').val(parent.length ? parent.attr('data-row') : 0);
	});

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

	function update_display_name(context) {
		context.closest('.link_info').find('.display_name').html(context.val())
	}
//--></script>
<?= $this->builder->js('errors', $errors); ?>
