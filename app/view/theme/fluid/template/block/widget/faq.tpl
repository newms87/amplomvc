<div id="block-widget-faq" class="block">
	<div class="faq-groups">
		<? if (!empty($faqs)) { ?>
			<? foreach ($faqs as $group) { ?>
				<div class="group">
					<h3><?= $group['title']; ?></h3>

					<? if (!empty($group['questions'])) { ?>
						<div class="faq-list">
							<? foreach ($group['questions'] as $faq) { ?>
								<div class="faq">
									<div class="question"><?= $faq['question']; ?></div>
									<div class="answer"><?= html_entity_decode($faq['answer']); ?></div>
								</div>
							<? } ?>
						</div>
					<? } ?>

				</div>
			<? } ?>
		<? } ?>
	</div>
</div>
