<?php
class App_Model_Sale_VoucherTheme extends Model
{
	public function addVoucherTheme($data)
	{
		$voucher_theme_id = $this->insert('voucher_theme', $data);

		if (!empty($data['translations'])) {
			$this->translation->setTranslations('voucher_theme', $voucher_theme_id, $data['translations']);
		}

		return $voucher_theme_id;
	}

	public function editVoucherTheme($voucher_theme_id, $data)
	{
		$this->update('voucher_theme', $data, $voucher_theme_id);

		if (!empty($data['translations'])) {
			$this->translation->setTranslations('voucher_theme', $voucher_theme_id, $data['translations']);
		}
	}

	public function deleteVoucherTheme($voucher_theme_id)
	{
		$this->delete('voucher_theme', $voucher_theme_id);

		$this->translation->deleteTranslation('voucher_theme', $voucher_theme_id);
	}

	public function getVoucherTheme($voucher_theme_id)
	{
		return $this->queryRow("SELECT * FROM " . DB_PREFIX . "voucher_theme WHERE voucher_theme_id = " . (int)$voucher_theme_id);
	}

	public function getVoucherThemes($data = array(), $select = '', $total = false)
	{
		//Select
		if ($total) {
			$select = "COUNT(*) as total";
		} elseif (empty($select)) {
			$select = "*";
		}

		//From
		$from = DB_PREFIX . "voucher_theme";

		//Where
		$where = '1';

		if (isset($data['name'])) {
			$where .= " AND LCASE(name) like '%" . $this->escape(strtolower($data['name'])) . "%'";
		}

		//Order and Limit
		if (!$total) {
			if (!empty($data['sort']) && strpos($data['sort'], '__image_sort__') === 0) {
				if (!$this->db->hasColumn('voucher_theme', $data['sort'])) {
					$this->extend->enableImageSorting('voucher_theme', str_replace('__image_sort__', '', $data['sort']));
				}
			}

			$order = $this->extractOrder($data);
			$limit = $this->extractLimit($data);
		} else {
			$order = '';
			$limit = '';
		}

		//The Query
		$query = "SELECT $select FROM $from WHERE $where $order $limit";

		$result = $this->query($query);

		if ($total) {
			return $result->row['total'];
		}

		return $result->rows;
	}

	public function getVoucherThemeTranslations($voucher_theme_id)
	{
		$translate_fields = array(
			'name',
		);

		return $this->translation->getTranslations('voucher_theme', $voucher_theme_id, $translate_fields);
	}

	public function getTotalVoucherThemes($data = array())
	{
		return $this->getVoucherThemes($data, '', true);
	}
}
