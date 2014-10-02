<?= call('common/header'); ?>

<section id="home-page" class="content">

	<header class="row top-row section-style-a">
		<div class="wrap">
			<div class="heading col xs-12">
				<h1><?= _l("We Install James Hardie Siding"); ?></h1>

				<h2><?= _l("Get your quote in 3 easy steps:"); ?></h2>
			</div>

			<div class="homepage-icons">
				<div class="icon col xs-12 sm-4">
					<div class="sprite-img details">
						<img src="<?= theme_image('sprite-icons8.png'); ?>" alt="<?= _l("My Project"); ?>" title="<?= _l("Siding Project Details"); ?>"/>
					</div>
					<div class="icon-text">
						<?= _l("List the details<Br>of your project"); ?>
					</div>
				</div>
				<div class="icon col xs-12 sm-4">
					<div class="sprite-img contact">
						<img src="<?= theme_image('sprite-icons8.png'); ?>" alt="<?= _l("Contact Contractor"); ?>" title="<?= _l("Siding Project Contact"); ?>"/>
					</div>
					<div class="icon-text">
						<?= _l("Let us know how<br>to contact you"); ?>
					</div>
				</div>
				<div class="icon col xs-12 sm-4">
					<div class="sprite-img quote">
						<img src="<?= theme_image('sprite-icons8.png'); ?>" alt="<?= _l("Contactor Quote"); ?>" title="<?= _l("Siding Project Quote"); ?>"/>
					</div>
					<div class="icon-text">
						<?= _l("Receive your<br>siding quote"); ?>
					</div>
				</div>
			</div>

			<div class="col xs-12 call-to-action">
				<a class="button get-quote-btn">
					<?= _l("GET MY QUOTE"); ?>
					<b class="sprite arrow"></b>
				</a>
			</div>
		</div>
	</header>

	<div class="home-main row section-style-b">
		<div class="wrap">
			<h1><?= _l("Why Choose Metro?"); ?></h1>

			<div class="metro-icons col xs-12">
				<div class="icon">
					<div class="sprite-img handshake">
						<img src="<?= theme_image('sprite-icons8.png'); ?>" alt="<?= _l("Tailored"); ?>" title="<?= _l("Match your needs"); ?>"/>
					</div>
					<div class="icon-text">
						<?= _l("Projects Tailored to<br>Your Needs"); ?>
					</div>
				</div>
				<div class="icon">
					<div class="sprite-img hardhat">
						<img src="<?= theme_image('sprite-icons8.png'); ?>" alt="<?= _l("High Quality"); ?>" title="<?= _l("Quality Siding Workmanship"); ?>"/>
					</div>
					<div class="icon-text">
						<?= _l("Quality Workmanship"); ?>
					</div>
				</div>
				<div class="icon">
					<div class="sprite-img trusted">
						<img src="<?= theme_image('sprite-icons8.png'); ?>" alt="<?= _l("Satisfied Customers"); ?>" title="<?= _l("Just ask our customers!"); ?>"/>
					</div>
					<div class="icon-text">
						<?= _l("More than 10,000<br> Satisfied Customers"); ?>
					</div>
				</div>
				<div class="icon">
					<div class="sprite-img strong-arm">
						<img src="<?= theme_image('sprite-icons8.png'); ?>" alt="<?= _l("Reliable"); ?>" title="<?= _l("We are there for you"); ?>"/>
					</div>
					<div class="icon-text">
						<?= _l("Reliable Products"); ?>
					</div>
				</div>
				<div class="icon">
					<div class="sprite-img hour-glass">
						<img src="<?= theme_image('sprite-icons8.png'); ?>" alt="<?= _l("Long-Term"); ?>" title="<?= _l("Long-Term Customers!"); ?>"/>
					</div>
					<div class="icon-text">
						<?= _l("Long-Term Customer<br>Satisfaction"); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="home-form row section-style-c">
		<div class="wrap">
			<?= call('metro/form'); ?>
		</div>
	</div>

</section>

<?= call('common/footer'); ?>
