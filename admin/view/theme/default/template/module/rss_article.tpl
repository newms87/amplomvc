<?= $header; ?>
<div class="content">
	<?= $this->breadcrumb->render(); ?>
	<div class="box">
	<div class="heading">
		<h1><img src="<?= HTTP_THEME_IMAGE . 'module.png'; ?>" alt="" /> <?= $head_title; ?></h1>
		<div class="buttons"><a onclick="$('#form').submit();" class="button"><?= $button_save; ?></a><a href="<?= $cancel; ?>" class="button"><?= $button_cancel; ?></a></div>
	</div>
	<div class="content">
		<form action="<?= $action; ?>" method="post" enctype="multipart/form-data" id="form">
			<table class="form">
				<tr>
					<td><?= $entry_rss_settings; ?></td>
					<td>
							<label for='num_to_grab'><?= $entry_num_to_grab; ?></label><input type='text' maxlength='2' it='num_to_grab' size='3' name='num_to_grab' value='<?= $num_to_grab; ?>' />
							<label for='num_to_keep'><?= $entry_num_to_keep; ?></label><input type='text' maxlength='2' id='num_to_keep' size='3' name='num_to_keep' value='<?= $num_to_keep; ?>' />
							<label for='title_length'><?= $entry_title_length; ?></label><input type='text' maxlength='2' id='title_length' size='3' name='title_length' value='<?= $title_length; ?>' />
					</td>
				</tr>
				<tr>
					<td><?= $entry_rss_url; ?></td>
					<td>
							<input type='text' name='rss_feed_url' size='80' value='<?= $rss_feed_url; ?>' />
							<a class='button' id='rss_update_button' href='<?= $update_rss; ?>'><?= $button_update_rss; ?></a></td>
				</tr>
				<tr>
					<td><?= $entry_designer; ?></td>
					<td>
						<input type='text' id='article_title' size='30' maxlength='30' />
						<input type='text' id='article_url' size='50' />
						<a onclick='add_article();' class='button'>Add Article</a>
					</td>
				</tr>
				<tr>
					<td><?= $text_article_help; ?></td>
					<td>
						<ul id="featured_articles" class="scrollbox">
								<? $article_count=0;?>
						<? foreach ($featured_articles as $key=>$article) { ?>
							<li>
									<input type='text' class='article_title' name="featured_articles[<?= $article_count; ?>][title]" value='<?= $article['title']; ?>' size='30' maxlength='22' />
									<div class='rss_title_chars'></div>
									<input type='text' class='article_url' name="featured_articles[<?= $article_count++; ?>][url]" value='<?= $article['url']; ?>' size='50'	/>
									<img onclick='$(this).parent().remove();' src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />
							</li>
						<? } ?>
						</ul>
				</tr>
			</table>
			<table id="module" class="list">
				<thead>
					<tr>
				<td class="left"><?= $entry_limit; ?></td>
						<td class="left"><?= $entry_layout; ?></td>
						<td class="left"><?= $entry_position; ?></td>
						<td class="left"><?= $entry_status; ?></td>
						<td class="right"><?= $entry_sort_order; ?></td>
						<td></td>
					</tr>
				</thead>
				<? $module_row = 0; ?>
				<? foreach ($modules as $module) { ?>
				<tbody id="module-row<?= $module_row; ?>">
					<tr>
					<td class="left"><input type="text" name="rss_article_module[<?= $module_row; ?>][limit]" value="<?= $module['limit']; ?>" size="1" maxlength='2' /></td>
						<td class='left'><?= $this->builder->build('select',$layouts, "rss_article_module[$module_row][layout_id]", (int)$module['layout_id']); ?></td>
				<td class='left'><?= $this->builder->build('select',$positions, "rss_article_module[$module_row][position]", $module['position']); ?></td>
						<td class="left"><?= $this->builder->build('select',$statuses, "rss_article_module[$module_row][status]", (int)$module['status']); ?></td>
						<td class="right"><input type="text" name="rss_article_module[<?= $module_row; ?>][sort_order]" value="<?= $module['sort_order']; ?>" size="3" /></td>
						<td class="left"><a onclick="$('#module-row<?= $module_row; ?>').remove();" class="button"><?= $button_remove; ?></a></td>
					</tr>
				</tbody>
				<? $module_row++; ?>
				<? } ?>
				<tfoot>
					<tr>
						<td colspan="8"></td>
						<td class="left"><a onclick="addModule();" class="button"><?= $button_add_module; ?></a></td>
					</tr>
				</tfoot>
			</table>
		</form>
	</div>
</div>

<script type="text/javascript"><!--
function validate_rss_title(context){
	num_chars = context.val().length;
	max_chars = <?= $title_length; ?>;
	rv = context.siblings('.rss_title_chars');
	rv.html(num_chars + '/' + max_chars);
	if(num_chars >=max_chars)
			rv.addClass('full');
	else
			rv.removeClass('full');
}
$(document).ready(function(){
	$('.article_title').keyup(function(){validate_rss_title($(this));})
	.blur(function(){$(this).siblings('.rss_title_chars').html('');})
	.focus(function(){validate_rss_title($(this));});
	
	$('#featured_articles').sortable({revert:true});
	$('form input').change(function(){
				if($('#warn_before_update').length == 0)
						$('#rss_update_button').removeAttr('href').after("<div id='warn_before_update'>Please Save Changes Before Updating!!</div>");
				});
});
--></script>

<script type="text/javascript"><!--
var article_row = <?= $article_count; ?>;
function add_article(){
	title = $('#article_title').val().substr(0,$('#title_length').val());
	url = $('#article_url').val();
	if(!title || !url)return;
	html = '<li>';
	html += '	<input type="text" class="article_title" name="featured_articles[%artrow%][title]" value="' + title +'" size="30" maxlength="22" />';
	html += '	<input type="text" class="article_url" name="featured_articles[%artrow%][url]" value="'+ url + '" size="50"	/>';
	html += '	<img onclick="$(this).parent().remove();" src="<?= HTTP_THEME_IMAGE . 'delete.png'; ?>" />';
	html += '</li>';
	$('#featured_articles').append(html.replace(/%artrow%/g,article_row++));
	$('#article_title, #article_url').val('');
}
--></script>
<script type="text/javascript"><!--
var module_row = <?= $module_row; ?>;

function addModule() {
	html	= '<tbody id="module-row%modrow%">';
	html += '	<tr>';
	html += '		<td class="left"><input type="text" name="rss_article_module[%modrow%][limit]" value="5" size="1" maxlength="2" /></td>';
	html += '		<td class="left">' + "<?= $this->builder->build('select',$layouts,'rss_article_module[%modrow%][layout_id]'); ?>" + '</td>';
	html += '		<td class="left">' + "<?= $this->builder->build('select',$positions,'rss_article_module[%modrow%][position]','column_left'); ?>" + '</td>';
	html += '		<td class="left">' + "<?= $this->builder->build('select',$statuses,'rss_article_module[%modrow%][status]',1); ?>" + '</td>';
	html += '		<td class="right"><input type="text" name="rss_article_module[%modrow%][sort_order]" value="5" size="3" /></td>';
	html += '		<td class="left"><a onclick="$(\'#module-row%modrow%\').remove();" class="button"><?= $button_remove; ?></a></td>';
	html += '	</tr>';
	html += '</tbody>';
	
	$('#module tfoot').before(html.replace(/%modrow%/g,module_row));
	module_row++;
}
//--></script>
<?= $this->builder->js('errors', $errors); ?>
<?= $footer; ?>