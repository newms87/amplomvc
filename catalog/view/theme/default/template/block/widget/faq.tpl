<div id="widgetfaq" class="box">
	<div class="box_heading"><?= _l("Frequently Asked Questions"); ?></div>
	<div class="box_content">
		<? if (!empty($faqs)) { ?>
			<? foreach ($faqs as $faq_group) { ?>
				<div class="faq_group">
					<h3><?= $faq_group['group_title']; ?></h3>

					<? if (!empty($faq_group['questions'])) { ?>
						<div class="faq_questions">
							<? foreach ($faq_group['questions'] as $faq) { ?>
								<div class="faq_item">
									<div class="question"><?= $faq['question']; ?></div>
									<div class="answer"><?= $faq['answer']; ?></div>
								</div>
							<? } ?>
						</div>
					<? } ?>

				</div>
			<? } ?>
		<? } ?>
	</div>
</div>
