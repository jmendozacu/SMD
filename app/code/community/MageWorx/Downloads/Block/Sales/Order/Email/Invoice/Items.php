<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Sales_Order_Email_Invoice_Items extends Mage_Sales_Block_Order_Email_Invoice_Items
{
    public function _toHtml()
    {
        $html = parent::_toHtml();

        $fileIds = array();
        $order = $this->getOrder();
        if (is_object($order)) {
            foreach ($order->getAllItems() as $item) {
                $ids = Mage::getResourceSingleton('mageworx_downloads/relation')->getFileIds($item->getProductId());
                $fileIds = array_merge($fileIds, $ids);
            }
        }
        $fileIds = array_unique($fileIds);

        $files = Mage::getResourceModel('mageworx_downloads/files_collection');
        $files->addResetFilter()
            ->addFilesFilter($fileIds)
            ->addStatusFilter()
            ->addCategoryStatusFilter()
            ->addStoreFilter()
            ->addSortOrder();

        if (!$files->count()) {
            return $html;
        }

        $filesBlock = $this->getLayout()->createBlock('mageworx_downloads/link', 'downloads', array('ids' => $fileIds, 'is_email' => 1));
        $html .= $filesBlock->toHtml();
        return $html;
    }
}
