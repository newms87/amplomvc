<?php
class App_Controller_Block_Cart_Shipping extends Controller
{
	public function build()
	{
		$data['action'] = '';

		$defaults = array(
			'country_id'      => option('config_country_id'),
			'zone_id'         => '',
			'postcode'        => '',
			'shipping_method' => '',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$data[$key] = $_POST[$key];
			} elseif (isset($_SESSION[$key])) {
				$data[$key] = $_SESSION[$key];
			} else {
				$data[$key] = $default;
			}
		}

		//Build Address Form (for full address submission if needed)
		$this->form->init('address');
		$this->form->set_template('form/address');
		$this->form->set_fields('firstname', 'lastname', 'address_1', 'company', 'address_2', 'city');
		$this->form->show_form_tag(false);

		$data['form_address'] = $this->form->build();


		$data['shipping_method'] = !empty($data['shipping_method']) ? $data['shipping_method'] : false;

		//Ajax Urls
		$data['url_quote'] = site_url('block/cart/shipping/quote');

		//Action Buttons
		$data['apply'] = site_url('block/cart/shipping/apply');

		$data['redirect'] = $this->url->here();

		$data['countries'] = $this->Model_Localisation_Country->getActiveCountries();

		$this->response->setOutput($this->render('block/cart/shipping', $data));
	}

	public function quote()
	{
		$json = array();

		if (!$this->cart->hasProducts()) {
			$json['error']['warning'] = _l("Your cart is empty!");
		}

		if (!$this->cart->hasShipping()) {
			$json['error']['warning'] = sprintf(_l("Warning: No Shipping options are available. Please <a href=\"%s\">contact us</a> for assistance!"), site_url('page/contact'));
		}

		if (!$json) {
			$this->form->init('address');
			$this->form->set_fields('country_id', 'zone_id', 'postcode');

			if (!$this->form->validate($_POST)) {
				$json['error'] = $this->form->getError();
			} elseif (!$this->cart->canShipTo($_POST)) {
				$json['error']['shipping_address'] = $this->cart->getError('shipping_address');
			}
		}

		if (!$json) {
			$shipping_methods = $this->cart->getShippingMethods($_POST);

			if ($shipping_methods) {
				$json['shipping_method'] = $shipping_methods;
			} else {
				$json['error']['warning'] = sprintf(_l("Warning: No Shipping options are available. Please <a href=\"%s\">contact us</a> for assistance!"), site_url('page/contact'));
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function apply()
	{
		$json = array();

		if (empty($_POST['shipping_method'])) {
			if (!empty($_POST['redirect'])) {
				$this->message->add('warning', _l("Please choose a shipping method!"));
				redirect(urldecode($_POST['redirect']));
			} else {
				$json['error'] = _l("Please choose a shipping method!");
				$this->response->setOutput(json_encode($json));
				return;
			}
		}

		if (!empty($_POST['add_address'])) {
			if (!$this->cart->canShipTo($_POST)) {
				$json['error'] = $this->cart->getError('shipping_address');
			} else {
				$address_id = $this->address->add($_POST);
			}
		} else {
			$address = array(
				'customer_id' => $this->customer->getId(),
				'country_id'  => $_POST['country_id'],
				'zone_id'     => $_POST['zone_id'],
				'postcode'    => $_POST['postcode'],
			);

			$address_id = $this->address->exists($address);
		}

		if (!empty($address_id)) {
			if (!$this->cart->setShippingAddress($address_id)) {
				$json['error']['shipping_address'] = $this->cart->getError('shipping_address') . $address_id;
			} else {
				$result = $this->cart->setShippingMethod($_POST['shipping_method']);

				if (!empty($_POST['redirect'])) {
					if ($result) {
						$this->message->add('success', _l("Success: Your shipping estimate has been applied!"));
					} else {
						$this->message->add('warning', $this->cart->getError('shipping_method'));
					}

					redirect(urldecode($_POST['redirect']));
				}

				if ($result) {
					$json['success'] = _l("Success: Your shipping estimate has been applied!");
				} else {
					$json['error'] = $this->cart->getError('shipping_method');
				}
			}

		} else {
			$json['request_address'] = true;
		}

		$this->response->setOutput(json_encode($json));
	}
}
