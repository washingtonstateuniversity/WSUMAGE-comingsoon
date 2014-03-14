<?php
class Wsu_ComingSoon_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 * Return the config value for the passed key
	 *
	 * @param string $key if null or nothing passed current store is used
	 * @return string config value
	 */
	public function getConfig($root, $key, $storeId = null) {
		$path = 'comingsoon/'.$root.'/' . $key;
		return Mage::getStoreConfig($path, $storeId);
	}
}