<table class="form">
	<tr>
		<td valign="top">
			<a id="add-group" class="button">{{Add FAQ Group}}</a>
		</td>
		<td>
			<ul id="faq-groups">
				<? foreach ($settings['faqs'] as $row => $group) { ?>
					<? $row_name = "settings[faqs][$row]"; ?>

					<li class="faq-group" data-row="<?= $row; ?>">
						<input class="title" size="50" type="text" placeholder="{{Group Title}}" name="<?= $row_name; ?>[title]" value="<?= $group['title']; ?>"/>

						<ul class="faq-list">
							<? if (!empty($group['questions'])) { ?>
								<? foreach ($group['questions'] as $faq_row => $faq) { ?>
									<? $faq_name = $row_name . "[questions][$faq_row]"; ?>

									<li class="faq" data-row="<?= $faq_row; ?>">
										<input class="question" type="text" size="100" placeholder="{{Question}}" name="<?= $faq_name; ?>[question]" value="<?= $faq['question']; ?>"/>
										<textarea class="answer" rows="6" cols="60" placeholder="{{Answer}}" name="<?= $faq_name; ?>[answer]"><?= $faq['answer']; ?></textarea>
										<a class="delete button remove-faq">X</a>
									</li>
								<? } ?>
							<? } ?>
						</ul>

						<a class="add-faq button" class="add">{{Add Question}}</a>

						<a class="delete button remove-group text">{{Delete}}</a>
					</li>
				<? } ?>
			</ul>
		</td>
	</tr>
</table>

<script type="text/javascript">
	$('.faq-list').sortable();
	$('#faq-groups').sortable();

	$('#add-group').click(function () {
		$.ac_template('group_list', 'add');
	});

	$('.add-faq').click(function () {
		$(this).siblings('.faq-list').ac_template('faq_list', 'add');
	});

	$('.remove-group').click(function () {
		$(this).closest('.faq-group').remove();
	});

	$('.remove-faq').click(function () {
		$(this).closest('.faq').remove();
	});

	//Carousel Settings
	$('.faq-group[data-row="__ac_template__"] .faq-list').ac_template('faq_list', {defaults: <?= json_encode($settings['faqs']['__ac_template__']['questions']['__ac_template__']); ?>});
	$('#faq-groups').ac_template('group_list', {defaults: <?= json_encode($settings['faqs']['__ac_template__']); ?>});
</script>
