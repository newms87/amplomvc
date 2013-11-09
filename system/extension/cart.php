<?php
class System_Extension_Cart extends Controller
{
	public function __construct($registry)
	{
		parent::__construct($registry);

		require_once(DIR_SYSTEM . "extension/cartExtension.php");
	}

	public function renderCarts()
	{
		$carts = $this->tool->get_files_r(DIR_SYSTEM . "extension/cart/", array('php'), FILELIST_STRING);

		$inline = '';
		$extend = '';

		foreach ($carts as $cart) {
			$classname = "System_Extension_Cart_" . pathinfo($cart, PATHINFO_FILENAME);

			$class = $this->$classname;

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
