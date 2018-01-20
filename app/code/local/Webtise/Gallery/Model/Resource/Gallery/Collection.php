<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:33
 */

class Webtise_Gallery_Model_Resource_Gallery_Collection extends Mage_Catalog_Model_Resource_Collection_Abstract
{
    protected function _construct(){
        parent::_construct();
        $this->_init('gallery/gallery');
    }

    protected function _toOptionArray($valueField='entity_id', $labelField='title', $additional=array()){
        $this->addAttributeToSelect('title');
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    protected function _toOptionHash($valueField='entity_id', $labelField='title'){
        $this->addAttributeToSelect('title');
        return parent::_toOptionHash($valueField, $labelField);
    }

    public function getSelectCountSql(){
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(Zend_Db_Select::GROUP);
        return $countSelect;
    }
}