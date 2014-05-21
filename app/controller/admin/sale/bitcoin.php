<?php
class App_Controller_Admin_Sale_Bitcoin extends Controller
{
	public function index()
	{
		//Add Styles
		$this->document->addStyle(URL_THEME . 'style/style.css');
		$this->document->addScript(URL_RESOURCES . 'js/jquery/jquery.js', 50);
		$this->document->addScript(URL_RESOURCES . 'js/jquery/qrcode.js', 51);

		$data['styles']  = $this->document->renderStyles();
		$data['scripts'] = $this->document->renderScripts();

		$data['bitcoin_address'] = $this->bitstamp->getDepositAddress();

		$this->response->setOutput($this->render('sale/bitcoin', $data));
	}
}
