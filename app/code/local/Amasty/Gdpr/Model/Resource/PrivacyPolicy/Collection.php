<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Gdpr
 */


class Amasty_Gdpr_Model_Resource_PrivacyPolicy_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('amgdpr/privacyPolicy');
    }

    public function joinAdminUser()
    {
        $this->getSelect()
            ->joinLeft(
                array('u' => $this->getTable('admin/user')),
                'main_table.last_edited_by = u.user_id',
                array('fullname' => "CONCAT(firstname, ' ' ,lastname)")
            );
    }

    public function joinContent($storeId)
    {
        $this->getSelect()
            ->joinLeft(
                array('c' => $this->getTable('amgdpr/policyContent')),
                'main_table.id = c.policy_id AND store_id = ' . intval($storeId),
                array('content' => 'IF(c.id IS NOT NULL, c.content, main_table.content)')
            );
        return $this;
    }
}
