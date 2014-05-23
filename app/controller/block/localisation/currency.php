<?php
class App_Controller_Block_Localisation_Currency extends App_Controller_Block_Block
{
	public function build()
	{
		if (isset($_POST['currency_code'])) {
			$this->currency->set($_POST['currency_code']);

			if (isset($_POST['redirect'])) {
				redirect($_POST['redirect']);
			} else {
				redirect('common/home');
			}
		}

		$data['action'] = site_url('module/currency');

		$data['currency_code'] = $this->currency->getCode();

		$data['currencies'] = array();

		$results = $this->Model_Localisation_Currency->getActiveCurrencies();

		foreach ($results as $result) {
			if ($result['status']) {
				$data['currencies'][] = array(
					'title'        => $result['title'],
					'code'         => $result['code'],
					'symbol_left'  => $result['symbol_left'],
					'symbol_right' => $result['symbol_right']
				);
			}
		}

		$data['redirect'] = $this->url->here();

		$this->render('block/localisation/currency', $data);
	}
}
