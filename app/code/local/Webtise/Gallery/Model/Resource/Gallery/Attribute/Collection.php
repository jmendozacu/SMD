<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:33
 */

class Webtise_Gallery_Model_Resource_Gallery_Attribute_Collection extends Mage_Eav_Model_Resource_Entity_Attribute_Collection
{
    protected function _initSelect() {
    $this->getSelect()->from(array('main_table' => $this->getResource()->getMainTable()))
        ->where('main_table.entity_type_id=?', Mage::getModel('eav/entity')->setType('gallery_gallery')->getTypeId())
        ->join(
            array('additional_table' => $this->getTable('gallery/eav_attribute')),
            'additional_table.attribute_id=main_table.attribute_id'
        );
    return $this;
}
    public function setEntityTypeFilter($typeId) {
    return $this;
}
    public function addVisibleFilter() {
    return $this->addFieldToFilter('additional_table.is_visible', 1);
}
    public function addEditableFilter() {
    return $this->addFieldToFilter('additional_table.is_editable', 1);
}
}