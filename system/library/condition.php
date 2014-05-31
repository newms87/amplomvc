<?php
class Condition extends Library
{
	public function getConditions()
	{
		return array(
			'always'          => "Always",
			'user_logged'     => "The User is Logged In",
			'user_logged_out' => "The user is not Logged In",
		);
	}

	public function is($condition)
	{
		switch ($condition) {
			case 'always':
				return true;

			case 'user_logged':
				return $this->customer->isLogged();

			case 'user_logged_out':
				return !$this->customer->isLogged();
		}
	}
}
