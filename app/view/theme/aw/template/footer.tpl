<? if (show_area('below')) { ?>
	<section class="area-below row">
		<div class="wrap">
			<?= area('below'); ?>
		</div>
	</section>
<? } ?>
</main>

<footer class="site-footer">
	<div class="row sub-footer">
		<div class="wrap">
			<div class="col xs-12 sm-9 md-6">
				<div class="col md-visible md-8 contact-info desktop">
					<a class="site-email" href="mailto:<?= option('site_email'); ?>"><?= option('site_email'); ?></a>
					<a class="site-phone" href="tel:<?= preg_replace("/[^\\d]/", '', option('site_phone')); ?>"><?= option('site_phone'); ?></a>
				</div>

				<div class="social-links col xs-12 md-4">
					<?= block('extras/social_media'); ?>
				</div>

				<div class="col md-hidden md-8 contact-info mobile">
					<a class="site-email" href="mailto:<?= option('site_email'); ?>"><?= option('site_email'); ?></a>
					<a class="site-phone" href="tel:<?= preg_replace("/[^\\d]/", '', option('site_phone')); ?>"><?= option('site_phone'); ?></a>
				</div>
			</div>
		</div>
	</div>

	<div class="row main-footer">
		<div class="wrap">
			<div class="col md-visible md-4 left copyright">
				<span class="text">&copy;<?= date('Y') . ' ' . option('site_name'); ?></span>
			</div>

			<? if (has_links('footer')) { ?>
				<div id="links-footer" class="col xs-12 md-8 md-right">
					<div class="links">
						<?= links('footer'); ?>
					</div>
				</div>
			<? } ?>

			<div class="col md-hidden xs-12 copyright">
				<span class="text">&copy;<?= date('Y') . ' ' . option('site_name'); ?></span>
			</div>
		</div>
	</div>
</footer>
</section><!-- /#container -->
</body>
</html>
