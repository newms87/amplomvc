<?php
class Admin_Controller_Setting_DbRules extends Controller
{
	public function index()
	{
		$this->language->load('setting/db_rules');

		$this->document->setTitle(_l("DB Rules"));

		$this->getList();
	}

	public function insert()
	{
		$this->language->load('setting/db_rules');

		$this->document->setTitle(_l("DB Rules"));

		if ($this->request->isPost() && $this->validateForm()) {
			$store_id = $this->Model_Setting_DbRules->addDbRule($_POST);

			$this->message->add('success', _l("Success: You have modified db rules!"));

			$this->url->redirect('setting/db_rules');
		}

		$this->getForm();
	}

	public function update()
	{
		$this->language->load('setting/db_rules');

		$this->document->setTitle(_l("DB Rules"));

		if ($this->request->isPost() && $this->validateForm()) {
			$this->Model_Setting_DbRules->editDbRule($_GET['db_rule_id'], $_POST);

			$this->message->add('success', _l("Success: You have modified db rules!"));

			$this->url->redirect('setting/db_rules', 'store_id=' . $_GET['store_id']);
		}

		$this->getForm();
	}

	public function delete()
	{
		$this->language->load('setting/db_rules');

		$this->document->setTitle(_l("DB Rules"));

		if (isset($_GET['selected']) && $this->validateDelete()) {
			foreach ($_GET['selected'] as $db_rule_id) {
				$this->Model_Setting_DbRules->deleteDbRule($db_rule_id);
			}

			$this->message->add('success', _l("Success: You have modified db rules!"));

			$this->url->redirect('setting/db_rules');
		}

		$this->getList();
	}

	private function getList()
	{
		$this->template->load('setting/db_rules_list');

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("DB Rules"), $this->url->link('setting/db_rules'));

		$this->data['insert'] = $this->url->link('setting/db_rules/insert');
		$this->data['delete'] = $this->url->link('setting/db_rules/delete');

		$url = $this->get_url(array('page'));

		$db_rules = $this->Model_Setting_DbRules->getDbRules();

		foreach ($db_rules as &$db_rule) {
			$action = array(
				'text' => $this->_('text_edit'),
				'href' => $this->url->link('setting/db_rules/update', 'db_rule_id=' . $db_rule['db_rule_id'])
			);

			$db_rule['selected'] = isset($_GET['selected']) && in_array($result['db_rule_id'], $_GET['selected']);
			$db_rule['action']   = $action;
		}

		$this->data['db_rules'] = $db_rules;

		$this->data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function getForm()
	{
		$this->template->load('setting/db_rules_form');

		$db_rule_id = isset($_GET['db_rule_id']) ? $_GET['db_rule_id'] : null;

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("DB Rules"), $this->url->link('setting/db_rules'));

		if (!$db_rule_id) {
			$this->data['action'] = $this->url->link('setting/db_rules/insert');
		} else {
			$this->data['action'] = $this->url->link('setting/db_rules/update', 'db_rule_id=' . $db_rule_id);
		}

		$this->data['cancel'] = $this->url->link('setting/db_rules');

		$db_rule_info = $db_rule_id ? $this->Model_Setting_DbRules->getDbRule($db_rule_id) : null;

		$defaults = array(
			'table'       => '',
			'column'      => '',
			'escape_type' => '',
			'truncate'    => ''
		);

		foreach ($defaults as $d => $value) {
			if (isset($_POST[$d])) {
				$this->data[$d] = $_POST[$d];
			} elseif (isset($db_rule_info[$d])) {
				$this->data[$d] = $db_rule_info[$d];
			} elseif (!$db_rule_id) {
				$this->data[$d] = $value;
			}
		}

		$this->data['data_escape_types'] = array(
			0 => _l('Normal Escape'),
			1 => _l('No Escape'),
			2 => _l("Image"),
			3 => _l("Integer"),
			4 => _l("Float"),
			5 => _l("Datetime"),
		);

		$this->data['data_yes_no'] = array(
			1 => _l("Yes"),
			0 => _l("No"),
		);

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validateForm()
	{
		if (!$this->user->can('modify', 'setting/db_rules')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify db rules!");
		}

		if (empty($_POST['table'])) {
			$this->error['table'] = _l("Table is required");
		}

		if (empty($_POST['column'])) {
			$this->error['column'] = _l("Column is required");
		}

		return empty($this->error);
	}

	private function validateDelete()
	{
		if (!$this->user->can('modify', 'setting/db_rules')) {
			$this->error['warning'] = _l("Warning: You do not have permission to modify db rules!");
		}

		return empty($this->error);
	}

	private function get_url($filters = null)
	{
		$url     = '';
		$filters = $filters ? $filters : array(
			'sort',
			'order',
			'page'
		);
		foreach ($filters as $f) {
			if (isset($_GET[$f])) {
				$url .= "&$f=" . $_GET[$f];
			}
		}
		return $url;
	}
}
