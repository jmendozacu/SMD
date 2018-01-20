<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:30
 */

class Webtise_Gallery_Model_Resource_Gallery extends Mage_Catalog_Model_Resource_Abstract
{
    public function _construct() {

        $resource = Mage::getSingleton('core/resource');
        $this->setType('gallery_gallery')
            ->setConnection(
                $resource->getConnection('gallery_read'),
                $resource->getConnection('gallery_write')
            );

    }

    public function getMainTable() {
        return $this->getEntityTable();
    }
}