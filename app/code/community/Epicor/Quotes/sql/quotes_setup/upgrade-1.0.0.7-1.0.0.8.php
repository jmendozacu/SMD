<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$conn->modifyColumn(
    $this->getTable('quotes/quote'), 'status_id',
    array(
    'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
    'length' => '30',
    )
);

$statuses = array(
    1 => 'pending',
    2 => 'awaiting_acceptance',
    3 => 'expired',
    4 => 'rejected_customer',
    5 => 'rejected_admin',
    6 => 'accepted',
    7 => 'ordered'
);

$quotes = Mage::getModel('quotes/quote')->getCollection();
/* @var $quotes Epicor_Quotes_Model_Mysql4_Quote_Collection */

foreach ($quotes as $quote) {
    /* @var $quote Epicor_Quotes_Model_Quote */
    Mage::register('gqr-processing', true);
    $status = $quote->getStatusId();
    
    $newState = $statuses[$status];
    $quote->setStatusId($newState);
    
    $quote->save();
    Mage::unregister('gqr-processing');
}

