<?= $is_ajax ? '' : call('header'); ?>

<section id="home-page" class="content">

	<header class="row top-row section-style-a">
		<div class="wrap">
			<div class="heading col xs-12">
				<h1>{{We Install James Hardie Siding}}</h1>

				<h2>{{Get your quote in 3 easy steps:}}</h2>
			</div>

			<div class="homepage-icons">
				<div class="icon col xs-12 sm-4">
					<div class="sprite-img details">
						<img src="<?= theme_image('sprite-17.png'); ?>" alt="{{My Project}}" title="{{Siding Project Details}}"/>
					</div>
					<div class="icon-text">
						{{List the details<Br>of your project}}
					</div>
				</div>
				<div class="icon col xs-12 sm-4">
					<div class="sprite-img contact">
						<img src="<?= theme_image('sprite-17.png'); ?>" alt="{{Contact Contractor}}" title="{{Siding Project Contact}}"/>
					</div>
					<div class="icon-text">
						{{Let us know how<br>to contact you}}
					</div>
				</div>
				<div class="icon col xs-12 sm-4">
					<div class="sprite-img quote">
						<img src="<?= theme_image('sprite-17.png'); ?>" alt="{{Contactor Quote}}" title="{{Siding Project Quote}}"/>
					</div>
					<div class="icon-text">
						{{Receive your<br>siding quote}}
					</div>
				</div>
			</div>

			<div class="col xs-12 call-to-action">
				<a class="button get-quote-btn">
					{{GET MY QUOTE}}
					<b class="sprite arrow"></b>
				</a>
			</div>
		</div>
	</header>

	<div class="home-main row section-style-b">
		<div class="wrap">
			<h1>{{Why Choose Metro?}}</h1>

			<div class="metro-icons col xs-12">
				<div class="icon">
					<div class="sprite-img handshake">
						<img src="<?= theme_image('sprite-17.png'); ?>" alt="{{Tailored}}" title="{{Match your needs}}"/>
					</div>
					<div class="icon-text">
						{{Projects Tailored to<br>Your Needs}}
					</div>
				</div>
				<div class="icon">
					<div class="sprite-img hardhat">
						<img src="<?= theme_image('sprite-17.png'); ?>" alt="{{High Quality}}" title="{{Quality Siding Workmanship}}"/>
					</div>
					<div class="icon-text">
						{{Quality Workmanship}}
					</div>
				</div>
				<div class="icon">
					<div class="sprite-img trusted">
						<img src="<?= theme_image('sprite-17.png'); ?>" alt="{{Satisfied Customers}}" title="{{Just ask our customers!}}"/>
					</div>
					<div class="icon-text">
						{{More than 10,000<br> Satisfied Customers}}
					</div>
				</div>
				<div class="icon">
					<div class="sprite-img strong-arm">
						<img src="<?= theme_image('sprite-17.png'); ?>" alt="{{Reliable}}" title="{{We are there for you}}"/>
					</div>
					<div class="icon-text">
						{{Reliable Products}}
					</div>
				</div>
				<div class="icon">
					<div class="sprite-img hour-glass">
						<img src="<?= theme_image('sprite-17.png'); ?>" alt="{{Long-Term}}" title="{{Long-Term Customers!}}"/>
					</div>
					<div class="icon-text">
						{{Long-Term Customer<br>Satisfaction}}
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

<?= $is_ajax ? '' : call('footer'); ?>
