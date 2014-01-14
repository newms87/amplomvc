<?php
class Admin_Controller_Sale_Bitcoin extends Controller
{
	public function index()
	{
		//Add Styles
		$this->document->addStyle(HTTP_THEME_STYLE . 'style.css');
		$this->document->addScript(HTTP_JS . 'jquery/jquery.js', 50);
		$this->document->addScript(HTTP_JS . 'jquery/qrcode.js', 51);

		$this->data['styles']  = $this->document->renderStyles();
		$this->data['scripts'] = $this->document->renderScripts();

		$this->data['bitcoin_address'] = $this->bitstamp->getDepositAddress();

		$this->template->load('sale/bitcoin');

		$this->response->setOutput($this->render());
	}
}
