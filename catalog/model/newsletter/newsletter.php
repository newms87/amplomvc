<?php
class Catalog_Model_Newsletter_Newsletter extends Model 
{
	public function getNewsletter($newsletter_id)
	{
		$query = $this->get('newsletter', '*', $newsletter_id);
		
		if ($query->num_rows) {
			$query->row['newsletter'] = unserialize($query->row['data']);
			
			unset($query->row['data']);
			
			return $query->row;
		}
		
		return array();
	}
}