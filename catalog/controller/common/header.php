<?php
class Catalog_Controller_Common_Header extends Controller
{
	public function index()
	{
		$this->template->load('common/header');

		if ($this->config->get('config_debug') && isset($_SESSION['debug'])) {
			html_dump($_SESSION['debug'], 'debug');
			unset($_SESSION['debug']);
		}

		$this->data['title'] = $this->document->getTitle();

		$this->data['base'] = $this->url->is_ssl() ? $this->config->get('config_ssl') : $this->config->get('config_url');

		//Add Styles
		$this->document->addStyle(HTTP_THEME_STYLE . 'style.css');
		$this->document->addStyle(HTTP_JS . 'jquery/ui/themes/ui-lightness/jquery-ui.custom.css');
		$this->document->addStyle(HTTP_JS . 'jquery/colorbox/colorbox.css');

		//Add Scripts
		$this->document->addScript(HTTP_JS . 'jquery/jquery.js', 50);
		$this->document->addScript(HTTP_JS . 'jquery/ui/jquery-ui.js', 51);
		$this->document->addScript(HTTP_JS . 'common.js', 53);

		//TODO: Move this to admin Panel?
		$this->document->localizeVar('image_thumb_width', $this->config->get('config_image_thumb_width'));
		$this->document->localizeVar('image_thumb_height', $this->config->get('config_image_thumb_height'));

		//Add Theme Scripts
		$this->document->addScript(HTTP_THEME_JS . 'common.js', 56);

		//Page Head
		$this->data['direction']      = $this->language->getInfo('direction');
		$this->data['description']    = $this->document->getDescription();
		$this->data['keywords']       = $this->document->getKeywords();
		$this->data['canonical_link'] = $this->document->getCanonicalLink();
		$this->data['body_class']     = $this->tool->getSlug($this->url->getPath());

		$this->data['styles']  = $this->document->renderStyles();
		$this->data['scripts'] = $this->document->renderScripts();

		$this->language->set('lang', $this->language->getInfo('code'));

		$this->data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
		$this->data['statcounter']      = $this->config->get('config_statcounter');

		$this->language->load('common/header');

		$this->data['messages'] = $this->message->fetch();

		$this->data['icon'] = $this->image->get($this->config->get('config_icon'));

		$this->data['name'] = $this->config->get('config_name');

		$this->data['logo'] = $this->image->get($this->config->get('config_logo'));

		$this->data['page_header'] = $this->Model_Design_PageHeaders->getPageHeader();

		//Navigation
		$this->data['links_primary']   = $this->document->getLinks('primary');
		$this->data['links_secondary'] = $this->document->getLinks('secondary');


		//If the customer is logged in, build the account menu
		if ($this->customer->isLogged()) {
			$this->data['is_logged'] = true;
			//The Welcome Message
			$link_logged = array(
				'name'         => 'logged',
				'display_name' => $this->_('text_logged', $this->customer->info('firstname') . ' ' . $this->customer->info('lastname')),
				'sort_order'   => 0,
			);

			$this->document->addLink('account', $link_logged);

			//Account Link
			$link_account = array(
				'name'         => 'account',
				'display_name' => $this->_('text_account'),
				'href'         => $this->url->link('account/account'),
				'sort_order'   => 1,
			);

			$this->document->addLink('account', $link_account);

			//Cart Link
			$link_cart = array(
				'name'         => 'cart',
				'display_name' => $this->_('text_shopping_cart'),
				'href'         => $this->url->link('cart/cart', "redirect=" . preg_replace('/redirect=[^&]*/', '', $this->url->here())),
				'sort_order'   => 2,
			);

			$this->document->addLink('account', $link_cart);

			//Include the Checkout navigation link only if there are products in the cart
			if ($this->cart->hasProducts()) {
				$link_checkout = array(
					'name'         => 'checkout',
					'display_name' => $this->_('text_checkout'),
					'href'         => $this->url->link('checkout/checkout'),
					'sort_order'   => 3,
				);

				$this->document->addLink('account', $link_checkout);
			}

			$link_logout = array(
				'name'         => 'logout',
				'display_name' => $this->_('text_logout'),
				'href'         => $this->url->link('account/logout'),
				'sort_order'   => 4,
			);

			$this->document->addLink('account', $link_logout);

			$this->data['links_account'] = $this->document->getLinks('account');
		} else {
			$this->data['is_logged'] = false;

			$this->data['block_login'] = $this->getBlock('account/login', array('type' => 'header'));

			$link_register = array(
				'name'         => 'register',
				'display_name' => $this->_('text_register'),
				'href'         => $this->url->link('account/register'),
				'sort_order'   => 1,
			);

			$this->document->addLink('account', $link_register);

			$this->data['links_account'] = $this->document->getLinks('account');
		}

		if (!$this->cart->isEmpty()) {
			$link_cart = array(
				'name'         => 'cart',
				'display_name' => $this->_('text_shopping_cart'),
				'href'         => $this->url->link('cart/cart', "redirect=" . preg_replace('/redirect=[^&]*/', '', $this->url->here())),
				'sort_order'   => 2,
			);

			$this->document->addLink('cart', $link_cart);

			$link_checkout = array(
				'name'         => 'checkout',
				'display_name' => $this->_('text_checkout'),
				'href'         => $this->url->link('checkout/checkout'),
				'sort_order'   => 3,
			);

			$this->document->addLink('cart', $link_checkout);

			$this->data['links_cart'] = $this->document->getLinks('cart');
		}

		$this->data['home'] = $this->url->link('common/home');

		$this->data['social_networks'] = $this->getBlock('extras/social_media');

		if ($this->config->get('config_multi_language')) {
			$this->data['block_languages'] = $this->getBlock('localisation/language');
		}

		if ($this->config->get('config_multi_currency')) {
			$this->data['block_currencies'] = $this->getBlock('localisation/currency');
		}

		$this->children = array(
			'common/above_content',
		);

		$this->render();
	}
}