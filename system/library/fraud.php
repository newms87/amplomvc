<?php
class Fraud extends Library
{
	public function atRisk($data)
	{
		$risk_score = $this->getRiskScore($data);

		return $risk_score > $this->config->get('config_fraud_score');
	}

	public function getRiskScore($data)
	{
		$risk_score = $this->System_Model_Fraud->getOrderFraudRiskScore($data['order_id']);

		if (!$risk_score) {
			/*
			maxmind api
			http://www.maxmind.com/app/ccv

			paypal api
			https://cms.paypal.com/us/cgi-bin/?cmd=_render-content&content_ID=developer/e_howto_html_IPNandPDTVariables
			*/

			$request = array(
				'i'               => $data['ip'],
				'city'            => $data['payment_city'],
				'region'          => $data['payment_zone'],
				'postal'          => $data['payment_postcode'],
				'country'         => $data['payment_country'],
				'domain'          => substr(strrchr($data['email'], '@'), 1),
				'custPhone'       => $data['telephone'],
				'license_key'     => $this->config->get('config_fraud_key'),
				'user_agent'      => $data['user_agent'],
				'forwardedIP'     => $data['forwarded_ip'],
				'emailMD5'        => md5(strtolower($data['email'])),
				'accept_language' => $data['accept_language'],
				'order_amount'    => $this->currency->format($data['total'], $data['currency_code'], $data['currency_value'], false),
				'order_currency'  => $data['currency_code'],
			);

			if ($data['shipping_method']) {
				$request += array(
					'shipAddr'    => $data['shipping_address_1'],
					'shipCity'    => $data['shipping_city'],
					'shipRegion'  => $data['shipping_zone'],
					'shipPostal'  => $data['shipping_postcode'],
					'shipCountry' => $data['shipping_country'],
				);
			}

			$curl = curl_init('https://minfraud1.maxmind.com/app/ccv2r');

			curl_setopt($curl, CURLOPT_HEADER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request));

			$response = curl_exec($curl);

			curl_close($curl);

			if ($response) {
				$data = array();

				parse_str($response, $data);

				$this->System_Model_Fraud->addOrderFraud($data);

				$risk_score = $data['riskScore'];
			}
		}

		return $risk_score;
	}
}