<table class="form">
	<tr>
		<td valign="top"><a id="add_video" class="button"><?= _l("Add Video"); ?></a></td>
		<td>
			<ul id="video_list" class="easy_list">

				<? $settings['videos']['template_row'] = array(
					'title' => "%title%",
					'href'  => "%href%",
				); ?>

				<? $video_row = 0; ?>
				<? foreach ($settings['videos'] as $key => $video) { ?>
					<? $row = $key === 'template_row' ? '%row%' : $video_row++; ?>
					<li class="video <?= $key; ?>">
						<input id="title_<?= $row; ?>" class="video_title" size="50" type="text" name="settings[videos][<?= $row; ?>][title]" value="<?= $video['title']; ?>"/><br/>
						<input id="href_<?= $row; ?>" class="video_href" size="50" type="text" name="settings[videos][<?= $row; ?>][href]" value="<?= $video['href']; ?>"/>
						<a class="button delete text" onclick="$(this).closest('.video').remove()"><?= _l("Delete"); ?></a>
					</li>
				<? } ?>

			</ul>
		</td>
	</tr>
</table>

<script type="text/javascript">
	template = $('.video.template_row');
	var video_template = template[0].outerHTML;
	template.remove();

	var video_row = <?= $video_row; ?>;

	$('#add_video').click(function () {
		template = video_template
			.replace(/%row%/g, video_row++)
			.replace(/%title%/g, '<?= _l("Your Video Title"); ?>')
			.replace(/%href%/g, '<?= _l("http://www.urltoyourvideo.com/"); ?>');

		$('#video_list').append(template);
	});

	$('#video_list').sortable();
</script>
