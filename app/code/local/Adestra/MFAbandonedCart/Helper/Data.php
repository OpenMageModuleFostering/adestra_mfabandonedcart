<?php
/*
* Adestra_MFAbandonedCart_Helper
*/

class Adestra_MFAbandonedCart_Helper_Data extends Mage_Core_Helper_Abstract{
	
	
	public function updateCall() {
		return $this->_createCall('update');
	}

	public function purchaseCall() {
		return $this->_createCall('purchase');
	}

	
	private function _createCall($type = 'update') {
		$url = NULL;
		if (Mage::getStoreConfig('adestra/mfabandonedcart/enabled')) {
			$conversion = Mage::getModel('mfabandonedcart/conversion');
			if ($type == 'update') $conversion->updateCart();
			if ($type == 'purchase') $conversion->checkoutPurchase();
			if ($type == 'purchase' || ($type == 'update' && $conversion->getItemsCount() > 0)) {	
				// only build url when purchase call or non empty cart items list.								
				$url = $conversion->buildUrl($type);
				if (Mage::getStoreConfig('adestra/mfabandonedcart/debug')) {
					Mage::Log($conversion->_getFullData());
					Mage::Log($url);
				}	
			}
			return $this->_formatHtml($url);
		}						
		
	}
	
	
	private function _formatHtml($url = NULL) {
		$output = '<div id="mfabandonedcart-conversion" style="display:hidden">';	
		if (!empty($url)) {
			$output .= '<img src="'.$url.'" width="0" height="0"></div>';	
		}
		$output .= '</div>';	
		return $output;
		
	}
		
} 