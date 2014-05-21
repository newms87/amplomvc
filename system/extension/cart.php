<?php
class System_Extension_Cart extends Controller
{
	public function get($code)
	{
		global $registry;
		return $registry->get("App_Controller_Extension_Cart_" . $code);
	}

	public function renderCarts()
	{
		//TODO: Should only get installed Cart extensions...
		$carts = $this->tool->get_files_r(DIR_SITE . "app/controller/extension/cart/", array('php'), FILELIST_STRING);

		$inline = '';
		$extend = '';

		foreach ($carts as $cart) {
			$class = $this->get(pathinfo($cart, PATHINFO_FILENAME));

			if (method_exists($class, 'renderCart')) {
				$class->renderCart();
				$extend .= $class->output;
			}

			if (method_exists($class, 'renderInline')) {
				$class->renderInline();
				$inline .= $class->output;
			}
		}

		return array(
			'inline' => $inline,
			'extend' => $extend,
		);
	}
}
