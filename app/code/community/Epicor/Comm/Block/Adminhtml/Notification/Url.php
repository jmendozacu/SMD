<?php

class Epicor_Comm_Block_Adminhtml_Notification_Url extends Mage_Adminhtml_Block_Template
{
    public function getAdminLogUrl()
    {
        $route = 'adminhtml/epicorcomm_message_log/view';
        $param=array('source'=>'notification');
        return Mage::helper('adminhtml')->getUrl($route,$param);
    }
}

