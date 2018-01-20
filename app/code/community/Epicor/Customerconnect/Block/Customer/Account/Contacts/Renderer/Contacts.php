<?php

class Epicor_Customerconnect_Block_Customer_Account_Contacts_Renderer_Contacts extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{

    //   protected $updateList = Array();
    public function render(Varien_Object $row)
    {

        $customer = Mage::getModel('customer/customer');
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($row->getEmailAddress());
        $customer->getId() ? $is_ecc_customer = 1 : $is_ecc_customer = 0;
        $jsonArray = json_encode(array(
            'contact_code' => $row->getContactCode(),
            'name' => $row->getName(),
            'function' => $row->getFunction(),
            'telephone_number' => $row->getTelephoneNumber(),
            'fax_number' => $row->getFaxNumber(),
            'email_address' => $row->getEmailAddress(),
            'login_id' => $row->getLoginId(),
            'source' => $row->getSource(),
            'master_shopper' => $row->getMasterShopper(),
            'is_ecc_customer' => $is_ecc_customer
        ));

        $html = '<input type="hidden" class="details" name="details" value="' . htmlspecialchars($jsonArray) . '"/> ';
        $html .= $row->getName();

        if (Mage::registry('manage_permissions')) {
            $customerSession = Mage::getSingleton('customer/session');
            $commHelper = Mage::helper('epicor_comm');
            /* @var $commHelper Epicor_Comm_Helper_Data */
            $erpAccount = $commHelper->getErpAccountInfo();
            /* @var $erpAccount Epicor_Comm_Model_Customer_Erpaccount */
            $erpAccountId = $erpAccount->getId();
            /* @var $collection Mage_Customer_Model_Customer             */
            $collection = Mage::getModel('customer/customer')->getCollection();
            $collection->addAttributeToFilter('contact_code', $row->getContactCode());
            $collection->addAttributeToFilter('erpaccount_id', $erpAccountId);
            $customer = $collection->getFirstItem();
            /* @var $customer Mage_Customer_Model_Customer */

            $groups = array();

            if ($customer && !$customer->isObjectNew()) {
                $groupsArray = Mage::getModel('epicor_common/access_group_customer')->getCustomerAccessGroups($customer->getId());
                foreach ($groupsArray as $group) {
                    $groups[] = $group->getGroupId();
                }
            }

            $html .= '<input type="hidden" name="groups" value="' . implode(',', $groups) . '"/> ';
        }

        return $html;
    }

}
