<?php
class Catalog_Controller_Payment_Callback extends Controller
{
	public function index()
	{
		$this->language->load('payment/payment');

		if (!empty($_GET['code'])) {
			$extension = $this->System_Extension_Payment->get($_GET['code']);

			if (!empty($_GET['callback'])) {
				if (method_exists($extension, $_GET['callback'])) {
					$extension->{$_GET['callback']}();
				}
				else {
					$this->error_log->write("Payment Callback: The payment method callback requested $_GET[code]::$_GET[callback]() does not exist!");

					$this->message->add('warning', _l("The Payment Method %s requested encountered a problem. Please try a different Payment Method.", $extension->getInfo('name')));
				}
			}
			elseif (method_exists($extension, 'callback')) {
				$extension->callback();
			} else {
				$this->error_log->write("Payment Callback: The payment method requested $_GET[code] did not have the callback method!");

				$this->message->add('warning', _l("The Payment Method %s requested encountered a problem. Please try a different Payment Method.", $extension->getInfo('name')));
			}
		} else {
			$this->error_log->write("Payment Callback: No payment method was specified for the callback URL!");

			$this->message->add('warning', _l("There was no payment method specified. Please try checking out again. If you continue to have problems, please choose a different payment method or contact us at <a href=\"%s\">%s</a>", $this->config->get('config_email'), $this->config->get('config_email')));
		}
	}
}
