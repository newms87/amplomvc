<?php
	
$_['firstname'] = array(
	'type'=>'text',
	'label' => "First Name",
	'validation' => array('text', 1, 64),
	'required' => true,
);

$_['lastname'] = array(
	'type'=>'text',
	'label' => "Last Name",
	'validation' => array('text', 1, 64),
	'required' => true,
);

$_['email'] = array(
	'type'=>'text',
	'label' => 'Email',
	'validation' => 'email',
	'required' => true,
);

$_['telephone'] = array(
	'type'=>'text',
	'label' => 'Phone',
	'validation' => 'phone',
	'required' => false,
);

$_['fax'] = array(
	'type'=>'text',
	'label' => 'Fax',
	'validation' => 'phone',
	'required' => false,
);

$_['password'] = array(
	'type'=>'password',
	'label' => 'Password',
	'validation' => 'password',
	'required' => true,
);

$_['confirm'] = array(
	'type'=>'password',
	'label' => 'Confirm Password',
	'validation' => 'password',
	'required' => true,
);
