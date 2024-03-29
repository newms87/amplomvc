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
		</div>
	</div>

	<div class="row main-footer">
		<div class="wrap">
			<div class="col <?= has_links('footer') ? 'lg-6 left' : ''; ?> copyright">
				<div class="link-menu bar-separator">
					<span class="link text">&copy;<?= date('Y') . ' ' . option('site_name'); ?></span>
					<span class="link text address sm-visible"><?= option('site_address'); ?></span>
				</div>
			</div>

			<? if (has_links('footer')) { ?>
				<div class="nav nav-footer col xs-12 xs-center lg-6 lg-right">
					<?= build_links('footer'); ?>
				</div>
			<? } ?>
		</div>
	</div>
</footer>
</section><!-- /#container -->
<script>

</script>
</body>
</html>
