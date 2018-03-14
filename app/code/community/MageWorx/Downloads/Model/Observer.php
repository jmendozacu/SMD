<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Observer
{
    public function saveProductFiles(Varien_Event_Observer $observer)
    {
        $product = $observer->getProduct();
        $ids = $product->getDownloadsFilesIds();
        $productId = $product->getId();
        $relation = Mage::getSingleton('mageworx_downloads/relation');
        if ($productId && Mage::app()->getRequest()->getActionName() == 'save') {
            $relation->getResource()->deleteFilesProduct($productId);
        }
        if ($ids && $productId) {
            $ids = explode(',', $ids);
            $ids = array_unique($ids);
            foreach ($ids as $fileId) {
                $relation->setData(
                    array(
                        'file_id' => $fileId,
                        'product_id' => $productId
                    )
                );
                $relation->save();
            }
        }
    }

    public function addFilesOnCategory($observer)
    {
        $block = $observer->getBlock();

        if ($this->_isFilesOnCategoryOut($block)) {
            return $this;
        }

        $toolbar = $block->getLayout()->getBlock('product_list_toolbar');
        $isGridMode = $toolbar && $toolbar->getCurrentMode() && $toolbar->getCurrentMode() == 'grid';

        $html = $observer->getTransport()->getHtml();
        $filesHtml = Mage::app()->getLayout()
            ->createBlock(
                'mageworx_downloads/product_link', '',
                array(
                    'id' => $block->getProduct()->getId(),
                    'is_category' => true,
                    'is_grid_mode' => $isGridMode
                )
            )->toHtml();

        $observer->getTransport()->setHtml($html . $filesHtml);

        return $this;
    }


    protected function _isFilesOnCategoryOut($block)
    {
        if (!($block instanceof Mage_Catalog_Block_Product_Price)) {
            return true;
        }

        if (Mage::registry('current_category') && !Mage::registry('current_product')) {
            return true;
        }

        if (!Mage::helper('mageworx_downloads')->isEnableFilesOnCategoryPages()) {
            return true;
        }

        return false;
    }

    public function addCustomerDownloadsTab($observer)
    {
        $block = $observer->getBlock();
        if (!($block instanceof Mage_Adminhtml_Block_Customer_Edit_Tabs)) {
            return $this;
        }

        $urlParams = array('customer_id' => Mage::registry('current_customer')->getId());

        $block->addTabAfter(
            'downloads', array(
            'label'  => Mage::helper('mageworx_downloads')->__('File Downloads'),
            'class'  => 'ajax',
            'url'    => $block->getUrl('adminhtml/mageworx_downloads_files/customer', $urlParams)
            ), 'tags'
        );

        return $this;
    }

    public function showNotifications($observer)
    {
        $model  = Mage::getModel('mageworx_downloads/notification');
        $model->checkUpdate();
    }
}