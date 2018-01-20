<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:30
 */

class Webtise_Gallery_Model_Resource_Gallery_Tag_Category extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct() {
        $this->_init('gallery/gallery_tag_category', 'entity_id');
    }
}