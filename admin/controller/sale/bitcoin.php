<?php
class Admin_Controller_Sale_Bitcoin extends Controller
{
	public function index()
	{
		//Add Styles
		$this->document->addStyle(URL_THEME . 'style/style.css');
		$this->document->addScript(URL_RESOURCES . 'js/jquery/jquery.js', 50);
		$this->document->addScript(URL_RESOURCES . 'js/jquery/qrcode.js', 51);

		$this->data['styles']  = $this->document->renderStyles();
		$this->data['scripts'] = $this->document->renderScripts();

		$this->data['bitcoin_address'] = $this->bitstamp->getDepositAddress();

		$this->view->load('sale/bitcoin');

		$this->response->setOutput($this->render());
	}
}
