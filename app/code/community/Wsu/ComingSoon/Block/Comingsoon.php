<?php
class Wsu_ComingSoon_Block_Comingsoon extends Mage_Core_Block_Template {
	public function __construct() {
		parent::__construct();
		$this->setTemplate('wsu/comingsoon/comingsoon.phtml');
	}
}