<table class="form">
	<tr>
		<td valign="top"><a id="add_faq_group" class="button"><?= $button_add_faq_group; ?></a></td>
		<td>
			<ul id="faq_list" class="easy_list">

				<? $settings['faqs']['template_row'] = array(
					'group_title' => "%group_title%",
					'questions'   => array(
						'template_row' => array(
							'question' => "%question%",
							'answer'   => "%answer%",
						),
					),
				); ?>

				<? $group_row = 0; ?>
				<? $faq_row = 0; //row doesn't matter, just need unique id ?>
				<? foreach ($settings['faqs'] as $key => $faq_group) { ?>
					<? $g_row = $key === 'template_row' ? '%group_row%' : $group_row++; ?>
					<li class="faq_group <?= $key; ?>" group_row="<?= $g_row; ?>">
						<input id="group_title<?= $g_row; ?>" class="group_title" size="50" type="text" name="settings[faqs][<?= $g_row; ?>][group_title]" value="<?= $faq_group['group_title']; ?>"/>

						<ul class="faq_question_list easy_list">
							<? if (!empty($faq_group['questions'])) { ?>
								<? foreach ($faq_group['questions'] as $fkey => $faq) { ?>
									<? $f_row = $key === 'template_row' ? '%faq_row%' : $faq_row++; ?>
									<li class="faq_item <?= $fkey; ?>">
										<input id="question<?= $fkey; ?>" class="question" type="text" size="100" name="settings[faqs][<?= $g_row; ?>][questions][<?= $f_row; ?>][question]" value="<?= $faq['question']; ?>"/>
										<textarea id="answer<?= $fkey; ?>" class="answer" rows="6" cols="60" name="settings[faqs][<?= $g_row; ?>][questions][<?= $f_row; ?>][answer]"><?= $faq['answer']; ?></textarea>
										<a class="delete_button" onclick="$(this).closest('li').remove()"></a>
									</li>
								<? } ?>
							<? } ?>
							<li><a id="add_faq_item" onclick="add_faq_item($(this));" class="add_button"></a></li>
						</ul>

						<a class="delete_button text" onclick="$(this).closest('.faq_group').remove()"><?= $button_delete; ?></a>
					</li>
				<? } ?>

			</ul>
		</td>
	</tr>
</table>

<script type="text/javascript">//<!--
	template = $('.faq_item.template_row').removeClass('template_row');
	var faq_template = template[0].outerHTML;
	template.remove();

	template = $('.faq_group.template_row');
	var group_template = template[0].outerHTML;
	template.remove();

	var group_row = <?= $group_row; ?>;

	$('#add_faq_group').click(function () {
		template = group_template
			.replace(/%group_row%/g, group_row++)
			.replace(/%group_title%/g, '<?= $entry_group_title; ?>');

		$('#faq_list').append(template);
	});

	var faq_row = <?= $faq_row; ?>;

	function add_faq_item(context) {
		template = faq_template
			.replace(/%faq_row%/g, faq_row++)
			.replace(/%group_row%/g, context.closest('.faq_group').attr('group_row'))
			.replace(/%question%/g, '<?= $entry_question; ?>')
			.replace(/%answer%/g, '<?= $entry_answer; ?>');

		context.closest('li').before(template);
	}
//--></script>
