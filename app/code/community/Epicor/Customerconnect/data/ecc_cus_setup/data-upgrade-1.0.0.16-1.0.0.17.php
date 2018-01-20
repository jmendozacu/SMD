<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$statusArray = array(
    'pending' => 'Pending',
    'awaiting_acceptance' => 'Awaiting Acceptance',
    'expired' => 'Expired',
    'rejected_customer' => 'Rejected by Customer',
    'rejected_admin' => 'Rejected',
    'accepted' => 'Accepted',
    'ordered' => 'Ordered',
);

foreach ($statusArray as $code => $name) {
    $status = Mage::getModel('customerconnect/erp_mapping_erpquotestatus');

    $status->setCode($code);
    $status->setStatus($name);
    $status->setState($code);

    $status->save();
}