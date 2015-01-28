<?php

class Condition extends Library
{
	static $conditions = array(
		''                => "Always",
		'user_logged'     => "The User is Logged In",
		'user_logged_out' => "The user is not Logged In",
	);

	public function is($condition)
	{
		switch ($condition) {
			case 'user_logged':
				return is_logged();

			case 'user_logged_out':
				return !is_logged();
		}

		return true;
	}
}
