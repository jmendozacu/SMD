<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */ 

class MageWorx_Downloads_Block_Adminhtml_Files_Edit_Tab_Downloads_Guests extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mageworx/downloads/guests_history.phtml');
    }

    protected function getFileId()
    {
        return (int)Mage::app()->getFrontController()->getRequest()->getParam('id');
    }

    public function getGuestsData()
    {
        $historyModel = Mage::getModel('mageworx_downloads/history');

        $guestsData = new Varien_Object(
            array(
            'download_date' => $historyModel->getGuestsLastDownloadDate(),
            'downloads_count' => $historyModel->getGuestsCount()
            )
        );

        return $guestsData;
    }
}