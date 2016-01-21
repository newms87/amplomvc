<?= $is_ajax ? '' : call('admin/header'); ?>
<div class="section">
	<form action="<?= site_url('admin/navigation/save', 'navigation_group_id=' . $navigation_group_id); ?>" method="post" enctype="multipart/form-data" class="ctrl-save">
		<div class="box">
			<div class="heading">
				<div class="breadcrumbs col xs-12 md-6 left">
					<?= $is_ajax ? '' : breadcrumbs(); ?>
				</div>

				<div class="buttons col xs-12 md-6 md-right">
					<button>{{Save}}</button>
				</div>
			</div>
			<div class="section">
				<table class="form">
					<tr>
						<td class="required"> {{Navigation Group:}}</td>
						<td>
							<input type="text" name="name" value="<?= $name; ?>"/>
						</td>
					</tr>
					<tr>
						<td>{{Status:}}</td>
						<td>
							<?= build(array(
								'type'   => 'select',
								'name'   => 'status',
								'data'   => $data_statuses,
								'select' => $status
							)); ?>
						</td>
					</tr>

					<tr id="links_list_data">
						<td colspan="2">
							<div id="links_list" class="box">
								<div class="heading"><h3>{{Navigation Links:}}</h3></div>

								<div class="section">
									<div class="left">
										<a onclick="add_navigation_link()" class="button">{{Add Link}}</a>
									</div>

									<div class="right">
										<ul id="sorted_links">
											<? foreach ($links as $nav_id => $link) { ?>
												<li class="nav_link" data-row="<?= $nav_id; ?>" data-parent_id="<?= $link['parent_id']; ?>">
													<div class="link_info">
														<div class="link_name">
															<span class="display_name"><?= $link['display_name']; ?></span>
															<span class="show_link_edit" onclick="toggle_edit_link($(this))">
																<span class="edit_text">{{Edit Link}}</span>
																<img class="remove_link" src="<?= theme_url('image/delete.png'); ?>" onclick="$(this).closest('li').remove();"/>
															</span>
														</div>
														<div class="editable" style="display:none">
															<input type="hidden" class="parent_id" name="links[<?= $nav_id; ?>][parent_id]" value="<?= $link['parent_id']; ?>"/>

															<div class="link_entry_display_name">
																<label for="link_display_name_<?= $nav_id; ?>">{{Display Name:}}</label>
																<input id="link_display_name_<?= $nav_id; ?>" type="text" name="links[<?= $nav_id; ?>][display_name]" onkeyup="update_display_name($(this));" value="<?= $link['display_name']; ?>"/>
															</div>

															<div class="link_entry_name">
																<label for="link_name_<?= $nav_id; ?>">{{Name:}}</label>
																<input id="link_name_<?= $nav_id; ?>" type="text" name="links[<?= $nav_id; ?>][name]" value="<?= $link['name']; ?>"/>
															</div>
															<div class="link_entry_title">
																<label for="link_title_<?= $nav_id; ?>"><?= _l("Title: <span class=\"help\">Shows when hovering over link</span>"); ?></label>
																<input id="link_title_<?= $nav_id; ?>" type="text" name="links[<?= $nav_id; ?>][title]" value="<?= $link['title']; ?>"/>
															</div>
															<div class="link_entry_href">
																<label for="link_href_<?= $nav_id; ?>">{{URL / route}}</label>
																<input id="link_href_<?= $nav_id; ?>" type="text" name="links[<?= $nav_id; ?>][path]" value="<?= $link['path']; ?>"/>
															</div>
															<div class="link_entry_query">
																<label for="link_query_<?= $nav_id; ?>">{{Query}}</label>
																<input id="link_query_<?= $nav_id; ?>" type="text" name="links[<?= $nav_id; ?>][query]" value="<?= $link['query']; ?>"/>
															</div>
															<div class="link_entry_condition">
																<label for="link_condition_<?= $nav_id; ?>">{{Display When:}}</label>
																<?=
																build(array(
																	'type'   => 'select',
																	'name'   => "links[$nav_id][condition]",
																	'data'   => $data_conditions,
																	'select' => $link['condition'],
																	'#id'    => "link_condition_$nav_id",
																)); ?>
															</div>
															<div class="link_entry_status">
																<label for="link_status_<?= $nav_id; ?>">{{Target:}}</label>
																<?=
																build(array(
																	'type'   => 'select',
																	'name'   => "links[$nav_id][target]",
																	'data'   => array(
																		''        => 'Current Window',
																		'_blank'  => 'New Tab / Window',
																		'_parent' => "Parent Window",
																	),
																	'select' => $link['target'],
																	'#id'    => "link_target_$nav_id",
																)); ?>
															</div>
															<div class="link_entry_status">
																<label for="link_status_<?= $nav_id; ?>">{{Status:}}</label>
																<?=
																build(array(
																	'type'   => 'select',
																	'name'   => "links[$nav_id][status]",
																	'data'   => $data_statuses,
																	'select' => $link['status'],
																	'#id'    => "link_status_$nav_id",
																)); ?>
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
		</div>
	</form>
</div>

<script type="text/javascript">
	$('#sorted_links').ac_template('link_list', {defaults: <?= json_encode($links['__ac_template__']); ?>});

	function add_navigation_link() {
		var new_link = $.ac_template('link_list', 'add');

		new_link.find('ul').sortable({
			delay:       100,
			connectWith: "#links_list ul",
			placeholder: "ui-state-highlight",
			change:      show_placeholder
		});
	}

	$('.nav_link').each(function (i, e) {
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
			delay:       100,
			connectWith: "#links_list ul",
			placeholder: 'placeholder',
			change:      show_placeholder
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
			context.find('.edit_text').html('{{Edit Link}}');
		}
		else {
			edit.slideDown().addClass('showing');
			context.find('.edit_text').html('{{Hide}}');
		}
	}

	function update_display_name(context) {
		context.closest('.link_info').find('.display_name').html(context.val())
	}
</script>

<?= $is_ajax ? '' : call('admin/footer'); ?>
