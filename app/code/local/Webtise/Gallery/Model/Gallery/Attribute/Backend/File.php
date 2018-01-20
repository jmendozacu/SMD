<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 31/05/2016
 * Time: 16:15
 */

class Webtise_Gallery_Model_Gallery_Attribute_Backend_File extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function afterSave($object){
        $value = $object->getData($this->getAttribute()->getName());

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return;
        }

        $path = Mage::helper('gallery/gallery')->getFileBaseDir();

        try {
            $uploader = new Varien_File_Uploader($this->getAttribute()->getName());
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save($path);
            $object->setData($this->getAttribute()->getName(), $result['file']);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
        } catch (Exception $e) {
            if ($e->getCode() != 666){
                throw $e;
            }
            return;
        }
    }
}