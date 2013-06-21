<?php
class Catalog_Controller_Designers_Designers extends Controller 
{
	public function index()
	{
		$designer_id = isset($_GET['designer_id'])?$_GET['designer_id']:false;
		
		$this->language->load("designers/designers");
		
		$this->document->setTitle($this->_('heading_title'));
		
		if ($designer_id) {
			$this->template->load('designers/designer');
			
			$this->data['designer_id'] = $designer_id;
			
			$designer = $this->Model_Catalog_Designer->getDesigner($designer_id,true);
			
			if (!$designer) {
				$this->url->redirect($this->url->link('designers/designers'), 302);
			}
				
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('designers/designers'));
			$this->breadcrumb->add($designer['name'], $this->url->link('designers/designers', 'designer_id=' . $designer_id));
				
			$this->data['d_sort_by'] = $d_sort_by = isset($_GET['d_sort_by'])?$_GET['d_sort_by']:null;

			$products = $this->Model_Catalog_Designer->getDesignerProducts($designer, $d_sort_by);
			
			if (!$products) {
				$this->language->set('heading_title', $designer['name']);
				$this->data['continue'] = $this->url->link('common/home');
				$this->_('no_product_text', $this->url->site('designers'));
			}
			else {
				$this->data['the_page'] = $_SERVER['REQUEST_URI'];
				
				$this->document->setTitle(ucfirst($designer['name']));
				//$this->document->setDescription($designer['meta_description']);
				//$this->document->setKeywords($designer['meta_keyword']);
			
				$this->language->set('heading_title', ucfirst($designer['name']));
				
				$article_insert = array();
				$articles = array();
				$article_list = $this->Model_Catalog_Designer->getDesignerArticles($designer_id);
				
				
				//This specifies the location of the articles by number inserted
				$art_loc = array(
					0=>array('from'=>1,'to'=>min(5,count($products)-1)),
					1=>array('from'=>3,'to'=>count($products)-1),
				);
				
				$count = 0;
				foreach ($article_list as $a) {
					$articles[$a['article_id']] = $a;
					$articles[$a['article_id']]['description'] = html_entity_decode($a['description']);
					
					$location = isset($art_loc[$count])?rand($art_loc[$count]['from'],$art_loc[$count]['to']):rand(3,($first?$first:count($products)-1));
					
					$location = array_key_exists($location, $article_insert)?$location+1:$location;
					
					$article_insert[$location] = $a['article_id'];
					
					$count++;
				}

				$sections = array(0=>array('section_name'=>'All','products'=>array()));
				$sect_id = 0;
				
				$count = 0;
				foreach ($products as $p) {
					if ($sect_id !== (int)$p['section_id']) {
						//if we are sorting by something, do not add products to different sections,
						//just jumble them together under the 0=>'All' section.
						if(!$d_sort_by)
							$sect_id = (int)$p['section_id'];
						//But we need to keep the section ids/names for the product attribute filter at the top of the page.
						$sections[(int)$p['section_id']] = array('section_name'=>$p['section_name'], 'products'=>array());
					}
					
					//insert articles between products at the specified random location
					if (array_key_exists($count,$article_insert)) {
						$sections[$sect_id]['products']['article-'.$count] = $articles[$article_insert[$count]];
						unset($article_insert[$count]);
					}
					$count++;
										
					$sections[$sect_id]['products'][$p['product_id']] = $p;
					$sections[$sect_id]['products'][$p['product_id']]['name'] = $this->tool->limit_characters($p['name'],50);
					$sections[$sect_id]['products'][$p['product_id']]['price'] = $this->currency->format($p['price']);
					$sections[$sect_id]['products'][$p['product_id']]['special'] = (int)$p['special'] > 0 ?$this->currency->format($p['special']):null;
					$sections[$sect_id]['products'][$p['product_id']]['href'] = $this->url->link('product/product','product_id='.$p['product_id'].'&designer_id='.$designer_id);
					$sections[$sect_id]['products'][$p['product_id']]['image'] = $this->image->get($p['image']);
					$sections[$sect_id]['products'][$p['product_id']]['thumb'] = $this->image->resize($p['image'],$this->config->get('config_image_category_width'),$this->config->get('config_image_category_height'));
				}
				
				while (!empty($article_insert)) {
					$sections[$sect_id]['products'][] = $articles[array_pop($article_insert)];
				}
				uasort($sections,function ($a,$b) {if($a=='All'||$a>$b)return 1;});
				$this->data['section_products'] = $sections;
				
				$this->data['open_quote'] = $this->image->get('data/open_quote.png');
				$this->data['close_quote'] = $this->image->get('data/close_quote.png');
				
				$this->data['description'] = html_entity_decode($designer['description']);
				$image = $designer['image']?$designer['image']:'data/no_image.png';
				$this->data['designer_image'] = $this->image->resize($image, $this->config->get('config_image_manufacturer_width'),$this->config->get('config_image_manufacturer_height'));
				

				$flashsale_id = $this->Model_Catalog_Designer->is_flashsale_page($designer_id);
				if ($flashsale_id) {
					$this->data['flashsale_id'] = $flashsale_id;
					$this->data['flashsale_clock'] = $this->image->get('data/clock.png');
					$this->data['flashsale_link'] = $this->url->link('sales/flashsale','flashsale_id='.$this->data['flashsale_id']);
				}
				
				$this->data['num_cols'] = 3;
				
				$this->data['filter'] = isset($_GET['filter'])?$_GET['filter']:0;
				
				$this->data['sort_url'] = preg_replace("/\?.*/","",$this->data['the_page']);
				$this->data['sort_list'] = array('pd.name ASC'=>'Sort A-Z', 'pd.name DESC'=>'Sort Z-A',
															'price ASC'=>'Lowest Price', 'price DESC'=>'Highest Price');
															
				$this->data['share_status'] = $this->config->get('config_share_status');
			}
		}
		else {
			$this->breadcrumb->add($this->_('text_home'), $this->url->link('common/home'));
			$this->breadcrumb->add($this->_('heading_title'), $this->url->link('designers/designers'));
								
			$designers = $this->Model_Catalog_Designer->getDesigners();
			if (empty($designers)) {
				$this->data['continue'] = $this->url->link('common/home');
				$this->language->set('heading_title', $this->_('no_designers_heading'));
			}
			else {
				$this->data['polaroid'] = $this->image->resize('data/polaroid-1.png', 260,283);
				
				$this->data['fs_tac'] = $this->image->resize('data/pink_tac.png', 36,52);

				foreach ($designers as $key=>&$d) {
					if ($this->Model_Catalog_Designer->hasProducts($d['designer_id'])) {
						$d['image'] =$this->image->resize( (isset($d['image'])?$d['image']:"no_image.png") ,196,206);
						$d['href'] = $this->url->site($d['keyword']);
						$d['flashsale'] = $this->Model_Catalog_Designer->is_flashsale_page($d['designer_id'], true);
					}
					else {
						unset($designers[$key]);
					}
				}
				
				$this->data['sort_list'] = array('a-z'=>'Sort A-Z','z-a'=>'Sort Z-A', 'ending_soon'=>"Ending Soon");
				
			}
			
			$this->data['designers'] = $designers;
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
		
		$this->response->setOutput($this->render());
	}

	public function update_statuses()
	{
		echo "Finding Designers to Activate and Expired Designers...<br>";
		$activated_designers = $this->Model_Catalog_Designer->activateDesigners();
		$expired_designers = $this->Model_Catalog_Designer->expireDesigners();
		
		if ($activated_designers || $expired_designers) {
			$this->mail->init();
			
			$this->mail->setFrom($this->config->get('config_email'));
			$this->mail->setSender($this->config->get('config_name'));
			
			$emails = $this->config->get('mail_designer_active_emails');
			$this->mail->setTo($emails);
			
			$subject = $this->config->get('mail_designer_active_subject');
			$html = $this->config->get('mail_designer_active_message');
			
			foreach ($activated_designers as $d) {
				echo "$d[name] has been Activated!<br>";
				
				$insertables = array(
					'name' => $d['name'],
					'active_date' => $this->date->format($d['date_active'],'M d, Y H:i:s'),
					'designer_page' => $this->url->link('designer/designer','designer_id='.$d['manufacturer_id']),
					'admin_link' => $this->url->site('admin')
				);
				
				$subject = $this->tool->insertables($insertables, $subject);
				$html = $this->tool->insertables($insertables, $html);
				
				$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$this->mail->setHtml(html_entity_decode($html, ENT_QUOTES, 'UTF-8'));
				
				echo 'notifying ' . $emails . '<br>';
				$this->mail->send();
			}
			
			$emails = $this->config->get('mail_designer_expire_emails');
			$this->mail->setTo($emails);
			
			$subject = $this->config->get('mail_designer_expire_subject');
			$html = $this->config->get('mail_designer_expire_message');
			
			foreach ($expired_designers as $d) {
				echo "$d[name] has Expired<br>";
				
				$insertables = array(
					'name' => $d['name'],
					'active_date' => $this->date->format($d['date_expires'],'M d, Y H:i:s'),
					'designer_page' => $this->url->link('designer/designer','designer_id='.$d['manufacturer_id']),
					'admin_link' => $this->url->site('admin')
				);
				
				$subject = $this->tool->insertables($insertables, $subject);
				$html = $this->tool->insertables($insertables, $html);
				
				$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$this->mail->setHtml(html_entity_decode($html, ENT_QUOTES, 'UTF-8'));
				
				echo 'notifying ' . $emails . '<br>';
				$this->mail->send();
			}
		}

		echo "Done";
	}

	public function notify_expiring()
	{
		echo "Finding Designers Expiring Soon...<br>";
		$designers = $this->Model_Catalog_Designer->getExpiringSoon();
		$this->mail->init();
		
		$this->mail->setFrom($this->config->get('config_email'));
		$this->mail->setSender($this->config->get('config_name'));
		
		$emails = $this->config->get('mail_designer_expiring_emails');
		$this->mail->setTo(explode(',',preg_replace('/\s/','',$emails)));
		
		foreach ($designers as $d) {
			echo "$d[name] is expiring on $d[date_expires]<br>";
			$insertables = array(
				'name'			=> $d['name'],
				'expire_date'	=> $this->tool->format_date($d['date_expires'],'M d, Y'),
				'designer_page' => $this->url->link('designer/designer','designer_id='.$d['manufacturer_id']),
				'admin_link'	=> $this->url->site('admin'),
			);
			
			$subject = $this->tool->insertables($insertables, $this->config->get('mail_designer_expiring_subject'));
			$message = $this->tool->insertables($insertables, $this->config->get('mail_designer_expiring_message'));
			
			$this->mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$this->mail->setHtml(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
			
			echo 'notifying ' . $emails . '<br>';
			$this->mail->send();
		}
		echo "Done";
	}
}