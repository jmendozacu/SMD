<?php
/**
 * MageWorx
 * File Downloads & Product Attachments Extension
 *
 * @category   MageWorx
 * @package    MageWorx_Downloads
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_Downloads_Model_Notification
{
    /**
     * Notification cache key
     */
    const NOTIFICATION_CACHE_KEY = 'mageworx_downloads_notification';

    /**
     * @var array Notifications data
     */
    private $_notifications = array();

    /**
     * Constructor.
     * Initialize module notifications and add them to the list.
     */
    public function __construct()
    {
        $helper = Mage::helper('mageworx_downloads');
        $title = $helper->__("MageWorx File Downloads & Product Attachments: Change block types in CMS content.");
        $message = $helper->__(
            "Extension's block types have been changed since version 1.7.0.
            If you have module blocks added into CMS/Static content you need to change their code.
            Please read the chapter \"7. ADDING BLOCKS TO CMS/STATIC PAGES\" of the Extension's User Guide for more details."
        );

        $this->_add($title, $message);

        if (version_compare(Mage::getConfig()->getModuleConfig('Mage_Admin')->version, '1.6.1.2', 'ge')) {
            $title = $helper->__("MageWorx File Downloads & Product Attachments: Add blocks to white list.");
            $message = $helper->__(
                "According to SUPEE-6788 patch technical details all custom blocks need to be added manually to the allowed blocks list.
                Please go to System > Permissions > Blocks and add the folowing blocks to the white list:
<pre>
mageworx_downloads/link
mageworx_downloads/category_link
mageworx_downloads/product_link
</pre>
            "
            );
            $this->_add($title, $message, 'http://magento.com/security/patches/supee-6788-technical-details');
        }
    }

    /**
     * Check if notification need to be added to inbox. Add them if true.
     * @return $this
     */
    public function checkUpdate()
    {
        $cache = Mage::app()->getCache();
        if ($cache->load(self::NOTIFICATION_CACHE_KEY)) {
            return $this;
        }

        Mage::getModel('adminnotification/inbox')->parse(array_reverse($this->_notifications));

        $cache->save(
            Mage::getSingleton('core/date')->gmtDate(),
            self::NOTIFICATION_CACHE_KEY,
            array(self::NOTIFICATION_CACHE_KEY)
        );

        return $this;
    }

    /**
     * Add notification to the list.
     * @param $title
     * @param $message
     * @param string|null $url
     * @return $this
     */
    protected function _add($title, $message, $url = null)
    {
        $this->_notifications[] = array(
            'severity'    => Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR,
            'date_added'  => Mage::getSingleton('core/date')->gmtDate(),
            'title'       => $title,
            'description' => $message,
            'url'         => $url,
        );

        return $this;
    }
}