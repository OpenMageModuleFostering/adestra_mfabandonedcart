<?php
class Adestra_MFAbandonedCart_Model_Conversion {


    private $client_domain;
	private $capture_id; 	// Basket ID ??
    private $account_id;  	// MF Client account ID
	private $token;			// Magento session
	private $record;		// Customer details
	private $email;			
	private $order_value;
	private $items;			// Array of items
		

    function __construct() {
		$this->account_id = NULL;
		$this->client_domain = NULL;
		$this->capture_id = NULL;
		$this->record = array();
		$this->email = NULL;
		$this->order_value = NULL;
		$this->items = array();
		$this->_updateBasics();
    }
	
	private function _updateBasics() {	

		$this->account_id = Mage::getStoreConfig('adestra/mfabandonedcart/accountid');
		$this->capture_id = Mage::getStoreConfig('adestra/mfabandonedcart/captureid');
		$this->client_domain = Mage::getStoreConfig('adestra/mfabandonedcart/clientdomain');

		$model = Mage::getModel('mfabandonedcart/token');
		$sid = Mage::getSingleton("customer/session")->getEncryptedSessionId();
		$quote_id = Mage::getSingleton('checkout/session')->getQuoteId();
		$session_token = Mage::getSingleton('core/session')->getMFAbandonedCartTokenId();
		
		if ($session_token) $model->load($session_token);
		if(!$model->getTokenId()) $model->loadBySessionId($sid);
		if(!$model->getTokenId() && $quote_id > 0) $model->loadByQuoteId($quote_id);
		
		if(!$model->getTokenId()) {
			if($quote_id > 0) {
				$model->setTokenId($sid);
				$model->setSessionId($sid);
				$model->setQuoteId($quote_id);
				$model->save();	
			}
		}
		else {
			$model->setQuoteId($quote_id);
			$model->setSessionId($sid);
			$model->save();				
		}

		$this->token = $model->getTokenId();
		Mage::getSingleton('core/session')->setMFAbandonedCartTokenId($model->getTokenId()); 
//
//Mage::Log('************');
//Mage::Log(__FUNCTION__);
//Mage::Log('Mage sid: '.$sid);
//Mage::Log('MF token: '.Mage::getSingleton('core/session')->getMFAbandonedCartTokenId());
//Mage::Log('************');
		

//		$this->token = $token;
//		$this->token = Mage::getSingleton("customer/session")->getEncryptedSessionId();				
	}
	
	public function updateCart() {
				
		// Cart items.
		$this->items = array();		
		$cart = Mage::getModel('checkout/cart')->getQuote();
		foreach ($cart->getAllVisibleItems() as $item) {
			$conversion_item = array();
			$conversion_item['ref'] = $item->getProduct()->getSku();
			//$conversion_item['value'] = number_format($item->getProduct()->getPrice(), 2, '.', '');
			$conversion_item['value'] = number_format($item->getProduct()->getFinalPrice(), 2, '.', '');	// Final price includes discounts.
			$conversion_item['name'] = $item->getProduct()->getName();
			$conversion_item['quantity'] = $item->getQty();
			$conversion_item['image_url'] = urlencode($item->getProduct()->getThumbnailUrl());
			$this->items[] = $conversion_item;
		}
		
		
		// Customer details	
		$this->email = $cart->getCustomerEmail();
		$additional_core_fields = explode("\n",Mage::getStoreConfig('adestra/mfabandonedcart/additionalcorefields'));
		$this->record = array();
		foreach($additional_core_fields as $field) {
			$field = trim($field," \r");
			$field = explode("|",$field);
			switch ($field[0]) {
			case 'prefix':
			if ($cart->getCustomerPrefix()) $this->record[$field[1]] = $cart->getCustomerPrefix();
			break;
			case 'firstname':
			if ($cart->getCustomerFirstname()) $this->record[$field[1]] = $cart->getCustomerFirstname();
			break;
			case 'lastname':
			if ($cart->getCustomerLastname()) $this->record[$field[1]] = $cart->getCustomerLastname();
			break;
			case 'company':
			if ($cart->getBillingAddress()->getCompany()) $this->record[$field[1]] = $cart->getBillingAddress()->getCompany();
			break;			
			}
		}
		$this->order_value = number_format($cart->getGrandTotal(), 2, '.', '');
		
	}
	
	public function checkoutPurchase() {
		
//		 $order = Mage::getModel('sales/order')->loadByIncrementId(Mage::getSingleton('checkout/session')->getLastRealOrderId());
//		 $this->items = array();		
//	
//		foreach ($order->getAllVisibleItems() as $item) {
//			$conversion_item = array();
//			$conversion_item['ref'] = $item->getSku();
//			$conversion_item['value'] = number_format($item->getPrice(), 2, '.', '');
//			$conversion_item['name'] = $item->getName();
//			$conversion_item['quantity'] = $item->getQtyToInvoice();
//			$this->items[] = $conversion_item;
//		}
//
//		// Customer details.
//		$this->email = $order->getCustomerEmail();
//		$this->order_value = number_format($order->getGrandTotal(), 2, '.', '');
		


	}

	// Helper function to return array of objects
	public function _getFullData() {
		return get_object_vars($this);
	}

    /*
	*  Build url as per documentation: http://new.adestra.com/doc/page/current/index/conversion-captures/capture-implementation
	*  Note image urls need to be urlencoded before and item is json encoded.
	*/
	public function buildUrl($action = 'update') {
		
		$url = $this->client_domain.'q/basket?action='.$action
				.'&account_id='.$this->account_id
			    .'&capture_id='.$this->capture_id
			    .'&token='.$this->token;
				
		if ($action == 'update') {
			  $url .= '&email='.$this->email.'&order_value='.$this->order_value;		
				
			if(!empty($this->record)) {
					$url .= '&record='.str_replace('"','%22',json_encode($this->record));	
			}
					
			if(!empty($this->items)) {
				foreach ($this->items as $item) {
					$url .= '&item='.str_replace('"','%22',json_encode($item));	
				}	
			}
		}
			
		return $url;						
	}
	
	// Helper function to workout if basket is empty.
	public function getItemsCount() {
		return count($this->items);	
	}

}

?>