<?php
 class Adestra_MFAbandonedCart_IndexController extends Mage_Core_Controller_Front_Action
 {
	public function indexAction(){
				
		$cart_url = Mage::getUrl('checkout/cart');
		if (isset($_GET['sid'])) $cart_url .= '?SID='.$_GET['sid'];
		header('Location: ' . $cart_url);		

	}
	
}
?>