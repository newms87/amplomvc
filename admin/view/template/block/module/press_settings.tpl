<table class="form">
	<tr>
		<td>
			<?= $entry_press_items;?>
			<div id="add_press_item" onclick="add_press_item()"><?= $button_add_press_item;?></div>
		</td>
		<td>
			<ul id="press_item_list">
			<? $press_id = 0;?>
			<? foreach($press_items as $press){ ?>
				<li class="press_item">
					<span class="press_item_info_left">
						<div class="press_image">
							<?= $this->builder->set_builder_template('click_image');?>
							<?= $this->builder->image_input("settings[press_items][$press_id][image]", $press['image'], $press['thumb'], $no_image, $thumb_width, $thumb_height);?>
						</div>
						<div class="press_url">
							<div class="press_url_entry"><?= $entry_press_url;?></div>
							<input type="text" name="settings[press_items][<?= $press_id;?>][href]" value="<?= $press['href'];?>" /> 
						</div>
					</span>
					<span class="press_description">
						<textarea class='ckedit' id='ckedit<?=$press_id;?>' name="settings[press_items][<?= $press_id;?>][description]"><?= $press['description'];?></textarea>
					</span>
					<div class='button_remove' onclick="$(this).parent().remove()"></div>
				</li>
				<? $press_id++;?>
			<? } ?>
			</ul>
		</td>
	</tr>
</table>

<ul id="press_template" style="display:none">
	<li class="press_item">
		<span class="press_item_info_left">
			<div class="press_image">
				<?= $this->builder->set_builder_template('click_image');?>
				<?= $this->builder->image_input("settings[press_items][%press_id%][image]", null, null, $no_image, $thumb_width, $thumb_height);?>
			</div>
			<div class="press_url">
				<input type="text" name="settings[press_items][%press_id%][href]" value="" /> 
			</div>
		</span>
		<span class="press_description">
			<textarea id="ckedit%press_id%" name="settings[press_items][%press_id%][description]"></textarea>
		</span>
		<div class="button_remove" onclick="$(this).parent().remove()" ></div>
	</li>
</ul>

<?= $this->builder->js('ckeditor');?>

<script type="text/javascript">//<!--
var press_id = <?= $press_id+1;?>;

function add_press_item(data){
	html = $($('#press_template').html().replace(/%press_id%/g,press_id));
	
	img_input = html.find('.image input');
	img_thumb = html.find('.image img');
	
	img_input.attr('id', img_input.attr('id') + press_id);
	img_thumb.attr('id', img_thumb.attr('id') + press_id);
	
	$('#press_item_list').append(html);
	
	init_ckeditor_for("ckedit" + press_id);
	
	press_id++;
}

$('#press_item_list').sortable({delay: 300});

$('#form').on('saving', function(){
	$('#press_template').remove();
});
//--></script>