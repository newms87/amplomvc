<?php
//Generic Address Form

$_['firstname'] = array(
	'type'       => 'text',
	'label'      => _l("First Name"),
	'validation' => array(
		'text',
		3,
		32
	),
	'required'   => true,
);

$_['lastname'] = array(
	'type'       => 'text',
	'label'      => _l("Last Name"),
	'validation' => array(
		'text',
		3,
		32
	),
	'required'   => true,
);

$_['company'] = array(
	'type'       => 'text',
	'label'      => _l("Company"),
	'validation' => array(
		'text',
		1,
		128
	),
	'required'   => false,
);

$_['address_1'] = array(
	'type'       => 'text',
	'label'      => _l("Address"),
	'validation' => array(
		'text',
		2,
		128
	),
	'required'   => true,
);

$_['address_2'] = array(
	'type'       => 'text',
	'label'      => _l("Address Line 2"),
	'validation' => array(
		'text',
		0,
		128
	),
	'required'   => false,
);

$_['city'] = array(
	'type'       => 'text',
	'label'      => _l("City"),
	'validation' => array(
		'text',
		1,
		128
	),
	'required'   => true,
);

$_['postcode'] = array(
	'type'       => 'text',
	'label'      => _l("Postal Code"),
	'validation' => 'postcode',
	'required'   => true,
);

$_['country_id'] = array(
	'type'          => 'select',
	'label'         => _l("Country"),
	'#class'        => 'country_select',
	'validation'    => 'not_empty_zero',
	'default_value' => 223,
	'required'      => true,
);

$_['zone_id'] = array(
	'type'       => 'select',
	'label'      => _l("State"),
	'#class'     => 'zone_select',
	'validation' => 'not_empty_zero',
	'required'   => true,
);

$_['default'] = array(
	'type'          => 'radio',
	'label'         => _l('Set As Default Address?'),
	'default_value' => 1,
	'required'      => false,
);

$_['submit_address'] = array(
	'type'  => 'submit',
	'value' => _l('Submit Address'),
);
