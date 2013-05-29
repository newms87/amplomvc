<?php
class ControllerModuleRssArticle extends Controller 
{
	protected function index($setting)
	{
		$this->template->load('module/rss_article');
		
		$this->language->load('module/rss_article');
		
		empty($setting['limit'])?$setting['limit']=3:'';
		
		$articles = $this->config->get("featured_articles");
		
		
		if(count($articles) == 0)return;
		
		$this->data['featured_articles'] = array_slice($articles,0,$setting['limit']);
		
		$this->render();
	}
	
	public function update()
	{
		$rss_article = $this->model_setting_setting->getSetting('rss_article');
		
		if (!empty($rss_article['rss_feed_url'])) {
			extract($rss_article);
			isset($featured_articles)?'':$featured_articles=array();
			isset($num_to_grab)?'':$num_to_grab=5;
			isset($num_to_keep)?'':$num_to_keep=10;
			isset($title_length)?'':$title_length=22;
			$xml  = simplexml_load_file($rss_feed_url);
			$articles = $this->tool->parse_xml_to_array($xml);
			
			foreach (array_slice($articles['entry'],0,$num_to_grab) as $entry) {
				$title = html_entity_decode($entry['title'][0], ENT_QUOTES);
				if((strlen($title) > ($title_length+2)))
					$title = substr($title,0,$title_length) . '...';
				$new_articles[] = array('title'=>htmlentities($title, ENT_QUOTES), 'url'=>$entry['link'][0]);
			}
			
			$rss_article['featured_articles'] = array_slice(array_merge($new_articles, $featured_articles),0,$num_to_keep);
			$this->editRSSArticleSetting($rss_article);
		}
		if (isset($_GET['redirect'])) {
			$this->data['rss_update_msg'] = "Updated RSS Feed!";
			$this->index();
		}
		else {
			echo "Updated RSS Feed!";
		}
	}

	public function editRSSArticleSetting($data)
	{
		$store_id = 0;
		$group = 'rss_article';
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");

		foreach ($data as $key => $value) {
			if (!is_array($value)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1'");
			}
		}
	}
}
