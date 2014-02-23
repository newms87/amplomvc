<?php
class Admin_Controller_Extension_Shipping_Flat extends Controller
{
	public function settings()
	{


		$this->document->setTitle(_l("Flat Rate Shipping"));

		if ($this->request->isPost() && $this->validate()) {
			$this->config->saveGroup('shipping_flat', $_POST);

			$this->message->add('success', _l("You have successfully updated Flat Rate Shipping settings"));

			$this->url->redirect('extension/shipping');
		}

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Shipping Extensions"), $this->url->link('extension/shipping'));
		$this->breadcrumb->add(_l("Flat Rate Shipping"), $this->url->link('shipping/flat'));

		$this->data['action'] = $this->url->link('shipping/flat');
		$this->data['cancel'] = $this->url->link('extension/shipping');

		//Entry Data
		$flat_info = array();

		if ($this->request->isPost()) {
			$flat_info = $_POST;
		} else {
			$flat_info = $this->config->loadGroup('shipping_flat');
		}

		$defaults = array(
			'rates'      => array(),
		);

		$this->data += $flat_info = $defaults;

		//Template Data
		$this->data['data_tax_classes'] = $this->Model_Localisation_TaxClass->getTaxClasses();
		$this->data['data_geo_zones']   = $this->Model_Localisation_GeoZone->getGeoZones();

		$this->data['data_rule_types'] = array(
			'item_qty' => _l("Product Quantity"),
			'weight'   => _l("Weight of Cart"),
		);

		//The Template
		$this->template->load('shipping/flat');

		//Dependencies
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//Render
		$this->response->setOutput($this->render());
	}

	private function validate()
	{
		if (!$this->user->can('modify', 'shipping/flat')) {
			$this->error['warning'] = _l("You do not have permission to modify Flat Rate Shipping");
		}

		if (empty($_POST['flat_rates'])) {
			$this->error['flat_rates'] = _l("You must specify at least 1 flat rate rule");
		} else {
			foreach ($_POST['flat_rates'] as $key => $rate) {
				if (empty($rate['title'])) {
					$this->error["flat_rates[$key][title]"] = _l("Title is required");
				} else {
					$_POST['flat_rates'][$key]['method'] = $this->tool->getSlug($rate['title']);

					foreach ($_POST['flat_rates'] as $key2 => $rate2) {
						if ($rate2['method'] == $rate['title']) {
							$_POST['flat_rates'][$key]['method'] .= "_" . uniqid();
						}
					}
				}

				switch ($rate['rule']['type']) {
					case 'item_qty':
						if (!preg_match("/^[0-9]+,?[0-9]*$/", $rate['rule']['value'])) {
							$this->error["flat_rates[$key][rule][value]"] = _l("You must specify the rule");
						} else {
							if (preg_match("/^[0-9]+$/", $rate['rule']['value'])) {
								$_POST['flat_rates'][$key]['rule']['value'] .= ",0";
							}
						}
						break;
					case 'weight':
						break;
					default:
						break;
				}
			}
		}

		return $this->error ? false : true;
	}
}