<?php
class Admin_Controller_Module_RssArticle extends Controller
{
	
	
	public function index()
	{
		$this->template->load('module/rss_article');

		$this->language->load('module/rss_article');

		$this->document->setTitle($this->_('heading_title'));
		
		if ($this->request->isPost() && $this->validate()) {

			$this->Model_Setting_Setting->editSetting('rss_article', $_POST);
			
			$this->message->add('success', $this->_('text_success'));
			
			$this->url->redirect($this->url->link('module/rss_article'));
		}
		
		$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
		$this->breadcrumb->add($this->_('text_module'), $this->url->link('extension/module'));
		$this->breadcrumb->add($this->_('heading_title'), $this->url->link('module/rss_article'));
		
		$this->data['action'] = $this->url->link('module/rss_article');
		$this->data['cancel'] = $this->url->link('extension/module');

		if (isset($_POST['rss_article'])) {
			$ff = $_POST['rss_article'];
		} else {
			$ff = $this->Model_Setting_Setting->getSetting('rss_article');
		}
		
		$this->data['featured_articles'] = isset($ff['featured_articles'])?$ff['featured_articles']:array();
		$this->data['rss_feed_url'] = isset($ff['rss_feed_url'])?$ff['rss_feed_url']:'';
		$this->data['num_to_grab'] = isset($ff['num_to_grab'])?$ff['num_to_grab']:5;
		$this->data['num_to_keep'] = isset($ff['num_to_keep'])?$ff['num_to_keep']:10;
		$this->data['title_length'] = isset($ff['title_length'])?$ff['title_length']:22;
		$this->data['update_rss'] = $this->url->link('module/rss_article/update','redirect=true');
		$this->data['modules'] = isset($ff['rss_article_module'])?$ff['rss_article_module']:array();;
		
		
		$layouts = $this->Model_Design_Layout->getLayouts();
		$this->data['layouts'] = array();
		foreach($layouts as $layout)
			$this->data['layouts'][$layout['layout_id']] = $layout['name'];
		
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	public function update()
	{
		$rss_article = $this->Model_Setting_Setting->getSetting('rss_article');
		
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
			$rss_article['featured_articles'] = array_slice(array_merge($new_articles, $featured_articles),0,$num_to_keep);;
			$this->Model_Setting_Setting->editSetting('rss_article',$rss_article);
			
			if (isset($_GET['redirect'])) {
				$this->message->add('success', "Successfully Updated the RSS Feed!");
				$this->url->redirect($this->url->link('module/rss_article'));
			}
			echo "Updated RSS Feed!";
			exit;
		}
		
	}
	
	private function validate()
	{
		if (!$this->user->hasPermission('modify', 'module/rss_article')) {
			$this->error['warning'] = $this->_('error_permission');
		}
				
		return $this->error ? false : true;
	}
}