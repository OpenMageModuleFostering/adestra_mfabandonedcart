<?php
 class Adestra_MFAbandonedCart_IndexController extends Mage_Core_Controller_Front_Action
 {
	public function indexAction(){
				
		$cart_url = Mage::getUrl('checkout/cart');
		if (isset($_GET['sid'])) {
			$token_id = $_GET['sid'];
			$token = Mage::getModel('mfabandonedcart/token')->load($token_id);
			
			if($token->getTokenId()) {
				$quote = Mage::getModel('sales/quote')->load($token->getQuoteId());
				if($quote->getId()) {
					Mage::getSingleton('checkout/session')->setQuoteId($quote->getId());
				}
				
				$sid = Mage::getSingleton("customer/session")->getEncryptedSessionId();
				if ($token->getSessionId() != $sid) {
					$token->setSessionId($sid);
					$token->save();					
				}
				//$cart_url .= '?SID='.$token->getTokenId();			
			}
			header('Location: ' . $cart_url);	
		}

	}
	
}
?>