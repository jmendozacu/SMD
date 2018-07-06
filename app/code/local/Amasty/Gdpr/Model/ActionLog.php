<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_ActionLog extends Mage_Core_Model_Abstract
{
    protected $statusNames = array(
        'consent_given' => 'Consent Given',
        'delete_request_submitted' => 'Delete Request Submitted',
        'delete_request_approved' => 'Delete Request Approved',
        'data_anonymised_by_customer' => 'Data Anonymised by Customer',
    );

    protected function _construct()
    {
        $this->_init('amgdpr/actionLog');
    }

    public function getOptions()
    {
        return $this->statusNames;
    }

    /**
     * @param $action
     * @param null $customerId
     * @throws Varien_Exception
     */
    public function logAction($action, $customerId = null)
    {
        if (!$customerId) {
            $customerId = Mage::getSingleton('customer/session')->getId();
        }

        $ip = Mage::getSingleton('amgdpr/visitor')->getRemoteIp();

        Mage::getModel('amgdpr/actionLog')
            ->setData(array(
                'customer_id' => $customerId,
                'ip' => $ip,
                'created_at' => Mage::getSingleton('core/date')->gmtDate(),
                'action' => $action
            ))
            ->save();
    }
}
