<?php
class Wsu_ComingSoon_Block_Maintenance extends Mage_Core_Block_Template {
	public function __construct() {
		parent::__construct();
		$this->setTemplate('wsu/comingsoon/maintenance.phtml');
	}
    public function getMessage() {
        return (string) Mage::getStoreConfig("wsu_comingsoon/maintenance/message_block",Mage::app()->getStore()->getId());
    }
}