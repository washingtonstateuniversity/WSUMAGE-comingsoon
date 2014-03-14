<?php
class Wsu_ComingSoon_Model_Adminhtml_System_Config_Source_Logging {

	/**
	 * Options getter
	 *
	 * @return array
	 */
	public function toOptionArray() {
		return array(
			array('value' => 0, 'label' => Mage::helper('Wsu_ComingSoon')->__('no logging')),
			array('value' => 1, 'label' => Mage::helper('Wsu_ComingSoon')->__('log only denied access')),
			array('value' => 2, 'label' => Mage::helper('Wsu_ComingSoon')->__('log all access')),
		);
	}

}
