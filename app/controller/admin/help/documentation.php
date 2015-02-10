<?php

class App_Controller_Admin_Help_Documentation extends Controller
{
	public function index()
	{
		set_page_info('title', _l("Documentation"));

		$s = $this->_('sections');
		$this->replace_tokens($s);
		$data['sections'] = $s;

		breadcrumb(_l("Home"), site_url('admin'));
		breadcrumb(_l("Documentation"), site_url('admin/help/documentation'));

		output($this->render('help/documentation', $data));
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
						$s = preg_replace("/%@[^%]*%@/", site_url(preg_replace("/%@/", '', $m)), $s, 1);
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
