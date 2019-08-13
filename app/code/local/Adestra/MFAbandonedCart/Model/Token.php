<?php

class Adestra_MFAbandonedCart_Model_Token extends Mage_Core_Model_Abstract {
    protected function _construct()
    {
        $this->_init('mfabandonedcart/token');		
    }
	
	public function loadBySessionId($sid) {
		$this->setData($this->getResource()->loadBySessionId($sid));
		return $this;
    }	

	public function loadByQuoteId($qid) {
		$this->setData($this->getResource()->loadByQuoteId($qid));
		return $this;
    }	

//	protected function _beforeSave() {
//		$this->setUpdatedAt(date("Y-m-d H:i:s", time()));
//		return parent::_beforeSave();
//	}
	
	public function save() {
		$this->setUpdatedAt(date("Y-m-d H:i:s", time()));
		if (!$this->getQuoteId()) $this->setQuoteId(0);

		$data = array(
					'session_id' => $this->getSessionId(), 
					'updated_at' => $this->getUpdatedAt(), 
					'quote_id' => $this->getQuoteId()
				);
		$this->getResource()->write($this->getTokenId(),$data);	
	}

} 
