<?php
class Catalog_Controller_Account_Success extends Controller
{
	public function index()
	{
		$this->template->load('common/success');
		$this->language->load('account/success');

		$this->document->setTitle(_l("Your Account Has Been Created!"));

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Account"), $this->url->link('account/account'));
		$this->breadcrumb->add(_l("Your Account Has Been Created!"), $this->url->link('account/success'));

		if (!$this->config->get('config_customer_approval')) {
			$this->_('text_message', $this->url->link('information/contact'));
		} else {
			$this->data['text_message'] = _l("<p>Thank you for registering with %s!</p><p>You will be notified by email once your account has been activated by the store owner.</p><p>If you have ANY questions about the operation of this online shop, please <a href=\"%s\">contact the store owner</a>.</p>", $this->config->get('config_name'), $this->url->link('information/contact'));
		}

		$this->data['continue'] = $this->url->link('account/account');

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		$this->response->setOutput($this->render());
	}
}
