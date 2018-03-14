<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Category_Link extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('mageworx/downloads/block_file_links.phtml');
    }

    protected function _prepareLayout()
    {
        $helper = Mage::helper('mageworx_downloads');
        $title = trim($this->getTitle());
        if (empty($title)) {
            $this->setTitle($helper->getFileDownloadsTitle());
        }

        $id = $this->getId();
        if (empty($id)) {
            return '';
        } else {
            $files = Mage::getResourceModel('mageworx_downloads/files_collection');
            $files->addResetFilter()
                ->addStatusFilter()
                ->addCategoryStatusFilter()
                ->addStoreFilter()
                ->addSortOrder();

            $sort = '';
            if ($id == 'all') {
                $this->setIsAllCategories(true);
            } else {
                $ids = explode(',', $id);
                $files->addCategoryFilter($ids);
                $this->setIsAllCategories(false);
            }

            $items = $files->getItems();
            foreach ($items as $k => $item) {
                if (!$helper->checkCustomerGroupAccess($item) && $helper->isHideFiles()) {
                    unset($items[$k]);
                }
            }

            if ($helper->getGroupByCategory()) {
                $items = $this->groupFiles($items);
            }

            $this->setItems($items);
        }

        return parent::_prepareLayout();
    }

    public function groupFiles($files)
    {
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