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
			<? if (has_links('footer')) { ?>
			<div class="links-footer col xs-12 sm-6 md-4 lg-3 left">
				<h4><?= _l("Useful Links"); ?></h4>

				<div class="links">
					<?= links('footer'); ?>
				</div>
			</div>
			<? } ?>

			<? if (has_links('footer-resources')) { ?>
			<div class="links-footer-resources col left xs-12 sm-6 md-4 lg-3">
				<h4><?= _l("Resources"); ?></h4>

				<div class="links">
					<?= links('footer-resources'); ?>
				</div>
			</div>
			<? } ?>

			<? if (has_links('footer-more')) { ?>
			<div class="links-footer-more col left xs-12 sm-6 md-4 lg-3">
				<h4><?= _l("More Information"); ?></h4>

				<div class="links">
					<?= links('footer-more'); ?>
				</div>
			</div>
			<? } ?>

		</div>

		<div id="footer-social-networks" class="xs-12 lg-3">
			<?= block('extras/social_media'); ?>
		</div>

	</div>
</footer>
</section><!-- /#container -->
</body>
</html>
