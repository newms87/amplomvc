<?= $header; ?>
<?= $column_left; ?><?= $column_right; ?>
<div class="content"><?= $content_top; ?>
	<?= $this->breadcrumb->render(); ?>
	<h1><?= _l("Checkout"); ?></h1>
	<? $step = 1; ?>
	<div id="checkout_process" class="checkout">
		<div id="login" class="clearfix">
			<div class="checkout-heading"><?= _l("Step") . ' ' . $step++; ?>. <?= _l("Login or Register a New Account"); ?></div>

			<? if (!empty($login_form)) { ?>
				<div class="section">
					<?= $login_form; ?>
				</div>
			<? } elseif ($guest_checkout) { ?>
				<div class="section">
					<h2><?= _l("Proceed with Guest Checkout"); ?></h2>

					<h3><a href="<?= $cancel_guest_checkout; ?>"><?= _l("Cancel Guest Checkout"); ?></a></h3>
				</div>
			<? } ?>

		</div>
		<div id="customer_information" class="checkout_item" route="block/checkout/customer_information">
			<div class="checkout-heading"><?= _l("Step") . ' ' . $step++; ?>. <?= _l("Shipping and Payment Information"); ?></div>
			<div class="checkout-content clearfix"></div>
		</div>
		<div id="confirm" class="checkout_item" route="block/checkout/confirm">
			<div class="checkout-heading"><?= _l("Step") . ' ' . $step++; ?>. <?= _l("Confirm Order"); ?></div>
			<div class="checkout-content clearfix"></div>
		</div>
	</div>
	<?= $content_bottom; ?>
</div>

<script type="text/javascript">

	$(document).ready(function () {
		<? if($logged || $guest_checkout){ ?>
		load_checkout_item('customer_information');
		<? } else{?>
		load_checkout_item('login');
		<? }?>
	});

	function load_checkout_item(c_item, route) {
		if (typeof c_item == 'string') {
			c_item = $('#' + c_item);
		}

		route = route || c_item.attr('route');

		if (!c_item || !route) return;

		$.ajax({
			url: "<?= HTTP_ROOT; ?>" + route,
			dataType: 'html',
			beforeSend: page_loading,
			complete: page_received,
			success: function (html) {
				c_content = c_item.find('.checkout-content');

				c_content.html(html);

				if ($('.active_checkout_item').length) {
					scroll_to = $('.active_checkout_item').position().top;
					$('body,html').animate({scrollTop: scroll_to}, 400);
				}

				$('.active_checkout_item .checkout-content').slideUp('slow')

				$('.active_checkout_item').removeClass('active_checkout_item');

				c_content.slideDown('slow');

				c_item.addClass('active_checkout_item');

				$('.checkout-heading a').remove();

				headings = <?= $logged ? 'c_item.prevUntil("#login")' : 'c_item.prevAll()'; ?>;

				headings.each(function (i, e) {
					$(e).find('.checkout-heading').append("<a class=\"modify\" onclick=\"load_checkout_item($(this).closest('.checkout_item'))\"><?= _l("Modify &raquo;"); ?></a>");
				});
			},
			error: handle_ajax_error
		});
	}

	function load_next_checkout_item() {
		clear_msgs();
		load_checkout_item($('.active_checkout_item').next());
	}

	function page_loading() {
		$('#checkout_process .button').attr('disabled', true);
		$('#checkout_process .button').after('<span class="wait">&nbsp;<img src="<?= HTTP_THEME_IMAGE . 'loading.gif'; ?>" alt="" /></span>');
	}

	function page_received() {
		$('#checkout_process .button').attr('disabled', false);
		$('.wait').remove();
	}

	function submit_checkout_item(context) {
		id = context.attr('id');

		if (id && id == 'button-account' && $('[name=account]:checked').val() == 'register') {
			load_checkout_item('customer_information', 'block/checkout/register');
			return;
		}

		load_next_checkout_item();
	}

	function validate_form(form, callback) {
		if (!form.attr('action')) return;

		data = form.serialize();

		//Add Submit name attribute to query
		form_submit = form.find('input[type=submit]');

		if (form_submit) {
			data += '&' + form_submit.attr('name') + '=' + form_submit.val();
		}

		//Add asynchronous ajax call flag
		data += '&async=1';

		$.ajax({
			url: form.attr('action'),
			type: 'post',
			data: data,
			dataType: 'json',
			success: function (json) {
				handle_validation_response(form, json);

				if (typeof callback == 'function') {
					callback(form, json);
				}
			},
			error: function (jqXHR, status) {
				handle_ajax_error(jqXHR, status);

				handle_validation_response(form, {});
			}
		});
	}

	function handle_validation_response(form, json) {
		json = json || {};

		if (json['redirect']) {
			location = json['redirect'];
			return;
		}

		form.find('.message_box, .error').remove();

		if (json['error']) {
			form.ac_msgbox('error', json['error'], true);
			form.ac_errors(json['error']);
		}
	}
</script>
<?= $footer; ?>
