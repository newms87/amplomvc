<?php
class Admin_Controller_Help_Documentation extends Controller
{
	public function index()
	{
		$this->view->load('help/documentation');

		$this->document->setTitle(_l("Documentation"));

		$s = $this->_('sections');
		$this->replace_tokens($s);
		$this->data['sections'] = $s;

		$this->breadcrumb->add(_l("Home"), $this->url->link('common/home'));
		$this->breadcrumb->add(_l("Documentation"), $this->url->link('help/documentation'));

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	function replace_tokens(&$section)
	{
		foreach ($section as &$s) {
			$matches = null;
			if (is_array($s)) {
				$this->replace_tokens($s);
			} else {
				if (preg_match_all("/%@[^%]*%@/", $s, $matches)) {
					foreach ($matches[0] as $m) {
						$s = preg_replace("/%@[^%]*%@/", $this->url->link(preg_replace("/%@/", '', $m)), $s, 1);
					}
				}
				if (preg_match_all("/%%[^%]*%%/", $s, $matches)) {
					foreach ($matches[0] as $m) {
						$s = preg_replace("/%%[^%]*%%/", "<span class ='n'>" . preg_replace("/%%/", '', $m) . "</span>", $s, 1);
					}
				}
				if (preg_match_all("/%![^%]*%!/", $s, $matches)) {
					foreach ($matches[0] as $m) {
						$s = preg_replace("/%![^%]*%!/", "<span class='important'>" . preg_replace("/%!/", '', $m) . "</span>", $s, 1);
					}
				}
			}
		}
	}
}
