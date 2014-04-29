<?php
class Catalog_Controller_Product_Compare extends Controller
{
	public function index()
	{
		if (!$this->session->has('compare')) {
			$this->session->set('compare', array());
		}

		if (isset($_GET['remove'])) {
			$key = array_search($_GET['remove'], $this->session->get('compare'));

			if ($key !== false) {
				unset($this->session->get('compare')[$key]);
			}

			$this->message->add('success', _l("Success: You have modified your product comparison!"));

			redirect('product/compare');
		}

		$this->document->setTitle(_l("Product Comparison"));

		$this->breadcrumb->add(_l("Home"), site_url('common/home'));
		$this->breadcrumb->add(_l("Product Comparison"), site_url('product/compare'));

		if ($this->session->has('success')) {
			$data['success'] = $this->session->get('success');

			$this->session->delete('success');
		} else {
			$data['success'] = '';
		}

		$data['products'] = array();

		$data['attribute_groups'] = array();

		foreach ($this->session->get('compare') as $key => $product_id) {
			$product_info = $this->Model_Catalog_Product->getProduct($product_id);

			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->image->resize($product_info['image'], option('config_image_compare_width'), option('config_image_compare_height'));
				} else {
					$image = false;
				}

				if ((option('config_customer_hide_price') && $this->customer->isLogged()) || !option('config_customer_hide_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id']));
				} else {
					$price = false;
				}

				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id']));
				} else {
					$special = false;
				}

				if ($product_info['quantity'] <= 0) {
					$availability = $product_info['stock_status'];
				} elseif (option('config_stock_display')) {
					$availability = $product_info['quantity'];
				} else {
					$availability = _l("In Stock");
				}

				$attribute_data = array();

				$attribute_groups = $this->Model_Catalog_Product->getProductAttributes($product_id);

				foreach ($attribute_groups as $attribute_group) {
					foreach ($attribute_group['attributes'] as $attribute) {
						$attribute_data[$attribute['attribute_id']] = $attribute['text'];
					}
				}

				$data['products'][$product_id] = array(
					'product_id'   => $product_info['product_id'],
					'name'         => $product_info['name'],
					'thumb'        => $image,
					'price'        => $price,
					'special'      => $special,
					'description'  => substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, 200) . '..',
					'model'        => $product_info['model'],
					'manufacturer' => $product_info['manufacturer'],
					'availability' => $availability,
					'rating'       => (int)$product_info['rating'],
					'reviews'      => sprintf(_l("Based on %s reviews."), (int)$product_info['reviews']),
					'weight'       => $this->weight->format($product_info['weight'], $product_info['weight_class_id']),
					'length'       => $this->length->format($product_info['length'], $product_info['length_class_id']),
					'width'        => $this->length->format($product_info['width'], $product_info['length_class_id']),
					'height'       => $this->length->format($product_info['height'], $product_info['length_class_id']),
					'attribute'    => $attribute_data,
					'href'         => site_url('product/product', 'product_id=' . $product_id),
					'remove'       => site_url('product/compare', 'remove=' . $product_id)
				);

				foreach ($attribute_groups as $attribute_group) {
					$data['attribute_groups'][$attribute_group['attribute_group_id']]['name'] = $attribute_group['name'];

					foreach ($attribute_group['attributes'] as $attribute) {
						$data['attribute_groups'][$attribute_group['attribute_group_id']]['attributes'][$attribute['attribute_id']]['name'] = $attribute['name'];
					}
				}
			} else {
				unset($this->session->get('compare')[$key]);
			}
		}

		$data['continue'] = site_url('common/home');

		$this->response->setOutput($this->render('product/compare', $data));
	}

	public function add()
	{
		$json = array();

		if (!$this->session->has('compare')) {
			$this->session->set('compare', array());
		}

		if (isset($_POST['product_id'])) {
			$product_id = $_POST['product_id'];
		} else {
			$product_id = 0;
		}

		$product_info = $this->Model_Catalog_Product->getProduct($product_id);

		if ($product_info) {
			if (!in_array($_POST['product_id'], $this->session->get('compare'))) {
				if (count($this->session->get('compare')) >= 4) {
					array_shift($this->session->get('compare'));
				}

				$this->session->get('compare')[] = $_POST['product_id'];
			}

			$json['success'] = sprintf(_l("Success: You have added <a href=\"%s\">%s</a> to your <a href=\"%s\">product comparison</a>!"), site_url('product/product', 'product_id=' . $_POST['product_id']), $product_info['name'], site_url('product/compare'));

			$json['total'] = sprintf(_l("Product Compare (%s)"), ($this->session->has('compare') ? count($this->session->get('compare')) : 0));
		}

		$this->response->setOutput(json_encode($json));
	}
}
