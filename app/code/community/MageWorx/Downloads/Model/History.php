<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_History extends Mage_Core_Model_Abstract
{
    /**
     * @var MageWorx_Downloads_Model_History
     */
    protected $_guestsCollection;

    public function _construct()
    {
        parent::_construct();
        $this->_init('mageworx_downloads/history');
    }

    /**
     * @return \MageWorx_Downloads_Model_Resource_History_Collection
     */
    public function getCustomerHistoryCollection()
    {
        return $this->getCollection()
            ->getDownloadsHistory()
            ->filterCustomers();
    }

    /**
     * @return \MageWorx_Downloads_Model_Resource_History_Collection
     */
    public function getGuestHistoryCollection()
    {
        if ($this->_guestsCollection) {
            return $this->_guestsCollection;
        }

        $this->_guestsCollection = $this->getCollection()
            ->getDownloadsHistory()
            ->filterGuests();

        return $this->_guestsCollection;
    }

    /**
     * Get count of files downloads by guests
     * @return int
     */
    public function getGuestsCount()
    {
        $item = $this->getGuestHistoryCollection()->getLastItem();
        if (!$item) {
            return 0;
        }
        return $item['downloads_count'];
    }

    /**
     * Get last date when the file was downloaded by guest
     * @return string
     */
    public function getGuestsLastDownloadDate()
    {
        $item = $this->getGuestHistoryCollection()->getLastItem();

        if (!$item) {
            return '';
        }
        return $item['last_download_date'];
    }
}