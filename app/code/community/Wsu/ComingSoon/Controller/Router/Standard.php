<?php
class Wsu_ComingSoon_Controller_Router_Standard extends Mage_Core_Controller_Varien_Router_Standard {

	public function match(Zend_Controller_Request_Http $request) {

		$helper = Mage::helper('wsu_comingsoon');
		$storeCode = $request->getStoreCodeFromPath();

		$coming_enabled = $helper->getConfig('coming','enabled', $storeCode);
		$maintenance_enabled = $helper->getConfig('maintenance','enabled', $storeCode);

		$currentUrl = Mage::helper('core/url')->getCurrentUrl();
		$grade = $request->getParam('grade');
		if(strpos($currentUrl,'adminhtml')!==false || $grade==1){
			$coming_enabled = 0;
			$maintenance_enabled = 0;
		}
		// module enabled?
		if ($maintenance_enabled == 1 || $coming_enabled == 1) {
			$root = $coming_enabled==1?'coming':'maintenance';
			$allowedIPsString = $helper->getConfig('allowedIPs', $storeCode);

			// remove spaces from string
			$allowedIPsString = preg_replace('/ /', '', $allowedIPsString);

			$allowedIPs = array();

			if ('' !== trim($allowedIPsString)) {
				$allowedIPs = explode(',', $allowedIPsString);
			}

			$currentIP = $_SERVER['REMOTE_ADDR'];

			$allowFrontendForAdmins = $helper->getConfig($root,'allowFrontendForAdmins', $storeCode);

			$adminIp = null;
			if (1 == $allowFrontendForAdmins) {
				//get the admin session
				Mage::getSingleton('core/session', array('name' => 'adminhtml'));

				//verify if the user is logged in to the backend
				$adminSession = Mage::getSingleton('admin/session');
				if ($adminSession->isLoggedIn()) {
					//do stuff
					$adminIp = $adminSession['_session_validator_data']['remote_addr'];
				}
			}

			if ($currentIP === $adminIp) {
				// current user is logged in as admin
				$this->__log('Access granted for admin with IP: ' . $currentIP . ' and store ' . $storeCode, 2, $storeCode);
			} else {
				// current user allowed to access website?
				if (!in_array($currentIP, $allowedIPs)) {
					$this->__log('Access denied  for IP: ' . $currentIP . ' and store ' . $storeCode, 1, $storeCode);

					$maintenancePage = trim($helper->getConfig($root,'maintenancePage', $storeCode));
					// if custom maintenance page is defined in backend, display this one
					if ('' !== $maintenancePage) {

						Mage::getSingleton('core/session', array('name' => 'front'));

						$response = $this->getFront()->getResponse();

						$response->setHeader('HTTP/1.1', '503 Service Temporarily Unavailable');
						$response->setHeader('Status', '503 Service Temporarily Unavailable');
						$response->setHeader('Retry-After', '5000');
						
						if($coming_enabled==1){
							$maintenancePage=file_get_contents(Mage::getBaseUrl() . 'index-coming.php');
						}
						$response->setBody($maintenancePage);
						$response->sendHeaders();
						$response->outputBody();
						exit();
					}
				} else {
					// i don't like this, switch out for something better
					$this->__log('Access granted for IP: ' . $currentIP . ' and store ' . $storeCode, 2, $storeCode);
				}
			}
		}

		return parent::match($request);
	}

	/**
	 * logging helper
	 * 
	 * @param type $string      text to log
	 * @param type $zendLevel   Zend_Log logging level
	 * @param type $verbosityLevelRequired verbosity (0 = no logging, 1 = only denied requests, 2 = denied and granted requests)
	 */
	private function __log($string, $verbosityLevelRequired = 1, $storeCode = null, $zendLevel = Zend_Log::DEBUG) {
		$helper = Mage::helper('wsu_comingsoon');
		$logFile = trim($helper->getConfig('settings','logFile', $storeCode));
		$logVerbosity = trim($helper->getConfig('settings','logVerbosity', $storeCode));

		if ('' === $logFile) {
			$logFile = 'maintenance.log';
		}

		if ($logVerbosity >= $verbosityLevelRequired) {
			Mage::log($string, $zendLevel, $logFile);
		}
	}

}