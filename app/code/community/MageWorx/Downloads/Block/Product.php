<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Product extends Mage_Core_Block_Template
{
    protected $_template = 'mageworx/downloads/block_file_links.phtml';

    protected function _prepareLayout()
    {
        $this->setData('template', $this->_template);
        $helper = Mage::helper('mageworx_downloads');

        if ($productBlock = $this->getLayout()->getBlock('product.info')) {
            $product = $productBlock->getProduct();
        } elseif ($prod = Mage::registry('current_product')) {
            $product = Mage::getModel('catalog/product')->load($prod->getId());
        } else {
            return $this;
        }

        $this->_setDownloadsTitle($product);

        $productId = (int)$product->getId();
        $items = $this->getProductFiles($productId);

        if (Mage::helper('mageworx_downloads')->getGroupByCategory() && $items && count($items)) {
            $items = $this->groupFiles($items);
        }

        if (count($items)) {
            $this->setItems($items);
        }

        if ($this->getNameInLayout() != 'downloads.tab' && $this->getItems()) {
            $position = $helper->getBlockPosition();
            if (in_array(1, $position)) {
                $productBlock->append($this, 'other');
            }
            if (in_array(2, $position)) {
                if ($additionalBlock = $this->getLayout()->getBlock('product.info.additional')) {
                    $additionalBlock->insert($this, '', false, 'downloads');
                }
            }
            if (in_array(3, $position)) {
                if ($tabsBlock = $this->getLayout()->getBlock('product.info.tabs')) {
                    $tabsBlock->addTab('downloads.tab', $this->getTitle(), $this->getType(), $this->_template);
                } else {
                    $this->setIsInTab(true);
                    $this->setBlockAlias('downloads.product');
                    $productBlock->addToChildGroup('detailed_info', $this);
                }
            }
        }
        return $this;
    }

    protected function _setDownloadsTitle($product)
    {
        $productDownloadsTitle = trim($this->helper('catalog/output')->productAttribute($product, $product->getDownloadsTitle(), 'downloads_title'));
        if ($productDownloadsTitle) {
            $this->setTitle($productDownloadsTitle);
        } else {
            $title = trim($this->getTitle());
            if (empty($title)) {
                $this->setTitle(Mage::helper('mageworx_downloads')->getProductDownloadsTitle());
            }
        }
    }

    public function getProductFiles($productId)
    {
        $_helper = Mage::helper('mageworx_downloads');
        $ids = Mage::getResourceSingleton('mageworx_downloads/relation')->getFileIds($productId);

        if (is_array($ids) && $ids) {
            $files = Mage::getResourceSingleton('mageworx_downloads/files_collection');
            $files->addResetFilter()
                ->addFilesFilter($ids)
                ->addStatusFilter()
                ->addCategoryStatusFilter()
                ->addStoreFilter()
                ->addSortOrder();

            $items = $files->getItems();
            foreach ($items as $k => $item) {
                if (!$_helper->checkCustomerGroupAccess($item) && $_helper->isHideFiles()) {
                    unset($items[$k]);
                }
            }

            return $items;
        }

        return false;
    }

    public function groupFiles($files)
    {
        if (!is_array($files)) {
            return $files;
        }

        $grouped = array();

        foreach ($files as $item) {
            $grouped[$item->getCategoryId()]['files'][] = $item;
            $grouped[$item->getCategoryId()]['title'] = '';
        }

        foreach ($grouped as $id => $cat) {
            if ($catModel = Mage::getModel('mageworx_downloads/categories')->load($id)) {
                $grouped[$id]['title'] = $catModel->getTitle();
            }
        }

        return $grouped;
    }

}
