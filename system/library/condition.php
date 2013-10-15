<?php
class Condition extends Library
{
	public function getConditions()
	{
		return array(
			'always'          => "Always",
			'cart_not_empty'  => "The Cart has Products",
			'cart_empty'      => "The Cart is Empty",
			'user_logged'     => "The User is Logged In",
			'user_logged_out' => "The user is not Logged In",
		);
	}

	public function is($condition)
	{
		switch ($condition) {
			case 'always':
				return true;

			case 'cart_not_empty':
				return !$this->cart->isEmpty();

			case 'cart_empty':
				return $this->cart->isEmpty();

			case 'user_logged':
				return $this->customer->isLogged();

			case 'user_logged_out':
				return !$this->customer->isLogged();
		}
	}
}
