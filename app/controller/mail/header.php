<?php

class App_Controller_Mail_Header extends Controller
{
	public function index($data = array())
	{
		if (empty($data['store'])) {
			if (!empty($data['store_id'])) {
				$data['store'] = $this->config->getStore($data['store_id']);
			} else {
				$data['store'] = array(
					'store_id' => 0,
					'name'     => option('config_name'),
					'url'      => site_url(),
				);
			}
		}

		$data += array(
			'title' => _l("%s", $data['store']['name']),
			'logo'  => $this->config->load('config', 'config_logo', $data['store']['store_id']),
		);

		if ($data['logo']) {
			$data['logo_width'] = option('config_email_logo_width', 400);
			$data['logo_height'] = option('config_email_logo_height', 150);

			if (!parse_url($data['logo'], PHP_URL_SCHEME) && strpos($data['logo'], '//') !== 0) {
				$data['logo'] = image($data['logo'], $data['logo_width'], $data['logo_height']);
			}

			if (!parse_url($data['logo'], PHP_URL_SCHEME)) {
				$data['logo'] = 'http://' . ltrim($data['logo'], '/\\');
			}
		}

		$this->render('mail/header', $data);
	}
}
