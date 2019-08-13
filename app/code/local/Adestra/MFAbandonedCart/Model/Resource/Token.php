<?php

class Adestra_MFAbandonedCart_Model_Resource_Token extends Mage_Core_Model_Resource_Db_Abstract {
    protected function _construct()
    {
        $this->_init('mfabandonedcart/token', 'token_id');		
    }
	
	public function loadBySessionId($sid)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('session_id = "'.$sid.'"');
        return $adapter->fetchRow($select);
    }	

	public function loadByQuoteId($qid)
    {
        $adapter = $this->_getReadAdapter();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('quote_id = "'.$qid.'"');
        return $adapter->fetchRow($select);
    }	


	public function write($token_id,$data) {
		
        $bindValues = array( 'token_id' => $token_id );
        $select = $this->_getReadAdapter()->select()
                ->from($this->getMainTable())
                ->where('token_id = :token_id');
        $exists = $this->_getReadAdapter()->fetchOne($select, $bindValues);

        $bind = array(
           // 'token_id' => $token_id,
            'session_id' => $data['session_id'],
            'updated_at' => $data['updated_at'],
			'quote_id' => $data['quote_id']		
        );
		
        if ($exists) {
            $where = array(
                'token_id=?' => $token_id
            );
            $this->_getWriteAdapter()->update($this->getMainTable(), $bind, $where);
        } else {
            $bind['token_id'] = $token_id;
            $this->_getWriteAdapter()->insert($this->getMainTable(), $bind);
        }
		
	}
} 
