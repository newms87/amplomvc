
			<? if (_area_has_blocks('below')) { ?>
				<section class="area-below row">
					<div class="wrap">
						<?= _area('below'); ?>
					</div>
				</section>
			<? } ?>
		</main>

		<footer class="row">
			<div class="wrap">

				<div id="links-footer">
					<h4><?= _l("Useful Links"); ?></h4>
					<div class="links">
						<?= _links('footer'); ?>

						<div id="footer-social-networks">
							<?= _block('extras/social_media'); ?>
						</div>
					</div>
				</div>

			</div>
		</footer>
	</section><!-- /#container -->
</body>
</html>
