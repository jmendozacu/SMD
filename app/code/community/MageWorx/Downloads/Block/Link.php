<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Block_Link extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('mageworx/downloads/block_file_links.phtml');
    }

    protected function _prepareLayout()
    {
        $helper = Mage::helper('mageworx_downloads');

        if ($this->getIsEmail()) {
            $this->setTemplate('mageworx/downloads/email_file_links.phtml');
        }

        $title = trim($this->getTitle());
        if (empty($title)) {
            $this->setTitle($helper->getFileDownloadsTitle());
        }

        $id = $this->getId();
        if (empty($id) && $this->getIds()) {
            $id = implode(',', $this->getIds());
        }

        if (empty($id)) {
            return '';
        } else {
            $ids = explode(',', $id);
            $files = Mage::getResourceModel('mageworx_downloads/files_collection');
            $files->addResetFilter()
                ->addFilesFilter($ids)
                ->addStatusFilter()
                ->addCategoryStatusFilter()
                ->addStoreFilter()
                ->addSortOrder();

            if ($this->getIsEmail()) {
                $files->addAttachFilter();
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
