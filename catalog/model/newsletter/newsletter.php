<?php
class Catalog_Model_Newsletter_Newsletter extends Model
{
	public function getNewsletter($newsletter_id)
	{
		$newsletter = $this->queryRow("SELECT * FROM " . DB_PREFIX . "newsletter WHERE newsletter_id = " . (int)$newsletter_id);

		if ($newsletter) {
			$newlsetter['newsletter'] = unserialize($newsletter['data']);

			unset($newsletter['data']);
		}

		return $newsletter;
	}
}