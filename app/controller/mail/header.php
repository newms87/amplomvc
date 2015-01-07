<?php

class App_Controller_Mail_Header extends Controller
{
	public function index($data = array())
	{
		$data += array(
			'title' => option('site_name'),
			'logo'  => str_replace("./", '', option('site_logo')),
		);

		if ($data['logo']) {
			$width = option('site_email_logo_width', 400);
			$height = option('site_email_logo_height', 150);

			if (!parse_url($data['logo'], PHP_URL_SCHEME) && strpos($data['logo'], '//') !== 0) {
				$data['logo'] = image($data['logo'], $width, $height);
			}

			$data['logo'] = cast_protocol($data['logo']);

			//Calculate width / height and scale if necessary
			if ($width && $height) {
				$data['logo_width'] = $width;
				$data['logo_height'] = $height;
			} else {
				$imagesize = getimagesize($data['logo']);

				if ($width || $height) {
					$data['logo_width'] = $width ? $width : $imagesize[0] * ($height/$imagesize[1]);
					$data['logo_height'] = $height ? $height : $imagesize[1] * ($width/$imagesize[0]);
				} else {
					$data['logo_width'] = $imagesize[0];
					$data['logo_height'] = $imagesize[1];
				}
			}
		}

		$this->render('mail/header', $data);
	}
}
