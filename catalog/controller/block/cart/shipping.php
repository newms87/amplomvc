<?php
class Catalog_Controller_Block_Cart_Shipping extends Controller
{
	public function index()
	{
		$this->template->load('block/cart/shipping');
		$this->language->load('block/cart/shipping');

		$this->data['action'] = '';

		$defaults = array(
			'country_id'      => $this->config->get('config_country_id'),
			'zone_id'         => '',
			'postcode'        => '',
			'shipping_method' => '',
		);

		foreach ($defaults as $key => $default) {
			if (isset($_POST[$key])) {
				$this->data[$key] = $_POST[$key];
			} elseif (isset($this->session->data[$key])) {
				$this->data[$key] = $this->session->data[$key];
			} else {
				$this->data[$key] = $default;
			}
		}

		//Build Address Form (for full address submission if needed)
		$this->form->init('address');
		$this->form->set_template('form/address');
		$this->form->set_fields('firstname', 'lastname', 'address_1', 'company', 'address_2', 'city');
		$this->form->show_form_tag(false);

		$this->data['form_address'] = $this->form->build();


		$this->data['shipping_method'] = !empty($this->data['shipping_method']) ? $this->data['shipping_method'] : false;

		//Ajax Urls
		$this->data['url_quote'] = $this->url->link('block/cart/shipping/quote');

		//Action Buttons
		$this->data['apply'] = $this->url->link('block/cart/shipping/apply');

		$this->data['redirect'] = $this->url->here();

		$this->data['countries'] = $this->Model_Localisation_Country->getCountries();

		$this->response->setOutput($this->render());
	}

	public function quote()
	{
		$this->language->load('block/cart/shipping');

		$json = array();

		if (!$this->cart->hasProducts()) {
			$json['error']['warning'] = $this->_('error_product');
		}

		if (!$this->cart->hasShipping()) {
			$json['error']['warning'] = sprintf(_l("Warning: No Shipping options are available. Please <a href=\"%s\">contact us</a> for assistance!"), $this->url->link('information/contact'));
		}

		if (!$json) {
			$this->form->init('address');
			$this->form->set_fields('country_id', 'zone_id', 'postcode');

			if (!$this->form->validate($_POST)) {
				$json['error'] = $this->form->get_errors();
			} elseif (!$this->cart->validateShippingAddress($_POST)) {
				$json['error']['shipping_address'] = $this->cart->get_errors('shipping_address');
			}
		}

		if (!$json) {
			$shipping_methods = $this->cart->getShippingMethods($_POST);

			if ($shipping_methods) {
				$json['shipping_method'] = $shipping_methods;
			} else {
				$json['error']['warning'] = sprintf(_l("Warning: No Shipping options are available. Please <a href=\"%s\">contact us</a> for assistance!"), $this->url->link('information/contact'));
			}
		}

		$this->response->setOutput(json_encode($json));
	}

	public function apply()
	{
		$this->language->load('block/cart/shipping');

		$json = array();

		if (empty($_POST['shipping_method'])) {
			if (!empty($_POST['redirect'])) {
				$this->message->add('warning', _l("Please choose a shipping method!"));
				$this->url->redirect(urldecode($_POST['redirect']));
			} else {
				$json['error'] = _l("Please choose a shipping method!");
				$this->response->setOutput(json_encode($json));
				return;
			}
		}

		if (!empty($_POST['add_address'])) {
			if (!$this->cart->validateShippingAddress($_POST)) {
				$json['error'] = $this->cart->get_errors('shipping_address');
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
				$json['error']['shipping_address'] = $this->cart->get_errors('shipping_address') . $address_id;
			} else {
				$result = $this->cart->setShippingMethod($_POST['shipping_method']);

				if (!empty($_POST['redirect'])) {
					if ($result) {
						$this->message->add('success', _l("Success: Your shipping estimate has been applied!"));
					} else {
						$this->message->add('warning', $this->cart->get_errors('shipping_method'));
					}

					$this->url->redirect(urldecode($_POST['redirect']));
				}

				if ($result) {
					$json['success'] = _l("Success: Your shipping estimate has been applied!");
				} else {
					$json['error'] = $this->cart->get_errors('shipping_method');
				}
			}

		} else {
			$json['request_address'] = true;
		}

		$this->response->setOutput(json_encode($json));
	}
}
