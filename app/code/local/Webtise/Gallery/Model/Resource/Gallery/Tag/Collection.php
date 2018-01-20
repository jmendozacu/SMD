<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:33
 */

class Webtise_Gallery_Model_Resource_Gallery_Tag_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct(){
        parent::_construct();
        $this->_init('gallery/gallery_tag');
    }
}