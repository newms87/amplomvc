
			<? if (show_area('below')) { ?>
				<section class="area-below row">
					<div class="wrap">
						<?= area('below'); ?>
					</div>
				</section>
			<? } ?>
		</main>

		<footer class="row">
			<div class="wrap">

				<div id="links-footer">
					<h4><?= _l("Useful Links"); ?></h4>
					<div class="links">
						<?= links('footer'); ?>

						<div id="footer-social-networks">
							<?= block('extras/social_media'); ?>
						</div>
					</div>
				</div>

			</div>
		</footer>
	</section><!-- /#container -->
</body>
</html>
