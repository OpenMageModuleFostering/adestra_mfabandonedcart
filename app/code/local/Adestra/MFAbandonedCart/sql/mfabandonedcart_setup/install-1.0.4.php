<?php

$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()->newTable($installer->getTable('mfabandonedcart/token'))
    ->addColumn('token_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        'primary' => true,
        ), 'Token ID')
    ->addColumn('session_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable' => false,
        ), 'Session ID')
    ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
        ), 'Quote ID')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        'nullable' => false,
        ), 'Update date')
    ->setComment('Adestra MessageFocus Abandoned Cart token table');	
$installer->getConnection()->createTable($table);
$installer->endSetup(); 

?>