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
			<div class="col xs-12 lg-6 contact-info">
				<? if ($site_email = option('site_email')) { ?>
					<a class="site-email" href="mailto:<?= $site_email; ?>">
						<i class="fa fa-envelope"></i>
						<?= $site_email; ?>
					</a>
				<? } ?>

				<? if ($site_phone = option('site_phone')) { ?>
					<a class="site-phone" href="tel:<?= preg_replace("/[^\\d]/", '', $site_phone); ?>">
						<i class="fa fa-phone"></i>
						<?= $site_phone; ?>
					</a>
				<? } ?>
			</div>

			<? /*
			<div class="social-links col xs-12 lg-3">
				<a class="link amp-sprite si-facebook-gray" target="_blank" href="https://www.facebook.com/RoofScope"></a>
				<a class="link amp-sprite si-twitter-gray" target="_blank" href="https://twitter.com/roofscope"></a>
				<a class="link amp-sprite si-googleplus-gray" target="_blank" href="https://plus.google.com/102164966680405341849/about?hl=en&partnerid=gplp0"></a>
			</div>
 */ ?>
		</div>
	</div>

	<div class="row main-footer">
		<div class="wrap">
			<div class="col lg-visible lg-6 left copyright">
				<span class="text">&copy;<?= date('Y') . option('site_name'); ?></span>
				<span class="text address"><?= option('site_address'); ?></span>
			</div>

			<? if (has_links('footer')) { ?>
				<div class="nav nav-footer col xs-12 xs-center lg-6 lg-right">
					<?= build_links('footer'); ?>
				</div>
			<? } ?>

			<div class="col lg-hidden xs-12 copyright">
				<span class="text">&copy;<?= date('Y') . option('site_name'); ?></span>
			</div>
		</div>
	</div>
</footer>
</section><!-- /#container -->
</body>
</html>
