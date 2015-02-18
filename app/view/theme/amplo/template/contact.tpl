<?= $is_ajax ? '' : call('header'); ?>

<div class="page contact-page section-style-a row">
	<div class="wrap">
		<h1 class="col xs-12 left"><?= _l("Contact Us"); ?></h1>

		<div class="contact-text col xs-12 lg-6 left top">
			<p>Questions, comments, concerns? Please let us know how we can help you!</p>

			<p>Our success wouldn't be possible without your feedback, so thanks a million for reaching out!</p>

			<div class="contact-map col xs-12 lg-visible left">
				<div id="contact-map-lg" class="google-map"></div>

				<div class="contact-info">
					<h5 class="site-name"><?= option('site_name'); ?></h5>

					<div class="address"><?= nl2br(option('site_address')); ?></div>
					<div class="phone"><?= option('site_phone'); ?></div>
					<div class="email"><?= option('site_email'); ?></div>
				</div>
			</div>
		</div>

		<div class="col lg-1 lg-visible"></div>

		<div class="contact-form col xs-12 lg-5 top">
			<form action="<?= site_url('contact/submit'); ?>" method="post" class="ajax-form">
				<div class="form-item">
					<input type="text" name="name" value="<?= $name; ?>" placeholder="{{*Name}}"/>
				</div>

				<div class="form-item">
					<input type="text" name="email" value="<?= $email; ?>" placeholder="{{*Email Address}}"/>
				</div>

				<div class="form-item">
					<textarea name="message" placeholder="{{Let us know any other details about your project...}}"><?= $message; ?></textarea>
				</div>

				<button data-loading="{{Contacting...}}">{{Send Message}}</button>
			</form>
		</div>

		<div class="contact-map col xs-12 lg-hidden left">
			<div id="contact-map-xs" class="google-map"></div>

			<div class="contact-info">
				<h5 class="site-name"><?= option('site_name'); ?></h5>

				<div class="address"><?= nl2br(option('site_address')); ?></div>
				<div class="phone"><?= option('site_phone'); ?></div>
				<div class="email"><?= option('site_email'); ?></div>
			</div>
		</div>
	</div>
</div>

<?
$lat = (float)option('site_address_lat');
$lng = (float)option('site_address_lng');

$replace = array(
	"\n",
	"\r",
);

$address = str_replace($replace, '', option('site_address'));
?>

<? if ($lat || $lng || $address) { ?>
<script type="text/javascript">


	function contact_map() {
		<? if ($lat || $lng) { ?>
		var pos = new google.maps.LatLng(<?= $lat; ?>, <?= $lng; ?>);
		init_google_map(pos);
		<? } else { ?>
		var geocoder = new google.maps.Geocoder();

		geocoder.geocode({'address': "<?= $address; ?>"}, function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				init_google_map(results[0].geometry.location);
			}
		});
		<? } ?>
	}

	function init_google_map(pos) {
		var options = {
			zoom:      <?= option('contact_map_zoom', 16); ?>,
			center: pos
		}

		$map = screen_width < 1024 ? $('#contact-map-xs') : $('#contact-map-lg');
		map = new google.maps.Map($map[0], options);

		//Setup Marker
		marker = new google.maps.Marker({
			position: pos,
			map:      map
		});
	}

	function load_google_map() {
		var script = document.createElement('script');
		script.type = 'text/javascript';
		script.src = 'https://maps.googleapis.com/maps/api/js?sensor=true&' +
		'callback=contact_map';
		document.body.appendChild(script);
	}

	window.onload = load_google_map();
</script>
<? } ?>

<?= $is_ajax ? '' : call('footer'); ?>
