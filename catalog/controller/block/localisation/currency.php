<?php
class Catalog_Controller_Block_Localisation_Currency extends Controller
{
	public function build()
	{
		if (isset($_POST['currency_code'])) {
			$this->currency->set($_POST['currency_code']);

			if (isset($_POST['redirect'])) {
				$this->url->redirect($_POST['redirect']);
			} else {
				$this->url->redirect('common/home');
			}
		}

		$data['action'] = $this->url->link('module/currency');

		$data['currency_code'] = $this->currency->getCode();

		$data['currencies'] = array();

		$results = $this->Model_Localisation_Currency->getCurrencies();

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
