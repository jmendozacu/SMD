<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 01/06/2016
 * Time: 11:49
 */

class Webtise_Gallery_Block_View_Custom extends Mage_Core_Block_Template
{
    protected $_galleryId;

    /**
     * If no template is set use default template
     *
     * @return $this
     */
    protected function _beforeToHtml() {
        parent::_beforeToHtml();
        if(!$this->getTemplate()) {
            $this->setTemplate('webtise/gallery/view/custom.phtml');
        }
        return $this;
    }

    /**
     * Validate gallery id set in xml
     *
     * @param string $id
     * @return bool
     */
    public function isValidGalleryId($id) {
        $galleries = Mage::getModel('gallery/gallery')->getCollection()
            ->addAttributeToFilter('status', 1)
            ->addAttributeToSelect(array('entity_id'));
        $ids = array();
        foreach($galleries as $gallery) {
            $ids[] = $gallery->getEntityId();
        }
        return in_array($id, $ids);
    }

    /**
     * Set id of gallery set in the xml
     * id is validated before being set
     *
     * @param string $id
     */
    public function setGalleryId($id) {
        if($this->isValidGalleryId($id)) {
            $this->_galleryId = $id;
        } else {
            $logger = Mage::getModel('core/log_adapter', 'gallery-exception.log');
            $logger->log('Cant validate gallery.');
        }
    }

    /**
     * Retrieve list of gallery images
     *
     * @return array|Varien_Data_Collection
     */
    public function getGalleryImages() {
        $gallery = $this->getGallery();
        if($gallery) {
            $collection = $gallery->getMediaGalleryImages();
            return $collection;
        }
        return false;
    }

    /**
     * Return Current Gallery
     *
     * @return mixed
     */
    public function getGallery() {
        return Mage::getModel('gallery/gallery')->load($this->_galleryId);
    }
}