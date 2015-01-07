<?php
class App_Model_Customer extends Model
{
	public function getCustomerGroups()
	{
		return $this->queryRows("SELECT * FROM " . self::$tables['customer_group']);
	}
}