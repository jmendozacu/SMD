<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 16/06/2016
 * Time: 11:16
 */

class Webtise_Gallery_Model_Gallery_Attribute_Backend_Media extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    protected $_renamedImages = array();

    /**
     * Load attribute data after gallery loaded
     *
     * @param Webtise_Gallery_Model_Gallery $object
     */
    public function afterLoad($object)
{
    $attrCode = $this->getAttribute()->getAttributeCode();
    $value = array();
    $value['images'] = array();
    $value['values'] = array();
    $localAttributes = array('label', 'related_product_ids', 'image_specific_url', 'tag_ids', 'position', 'disabled');

    foreach ($this->_getResource()->loadGallery($object, $this) as $image) {
        foreach ($localAttributes as $localAttribute) {
            if (is_null($image[$localAttribute])) {
                $image[$localAttribute] = $this->_getDefaultValue($localAttribute, $image);
            }
        }
        $value['images'][] = $image;
    }

    $object->setData($attrCode, $value);
}

    protected function _getDefaultValue($key, &$image)
{
    if (isset($image[$key . '_default'])) {
        return $image[$key . '_default'];
    }

    return '';
}

    /**
     * Validate media_gallery attribute data
     *
     * @param Webtise_Gallery_Model_Gallery $object
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function validate($object)
{
    if ($this->getAttribute()->getIsRequired()) {
        $value = $object->getData($this->getAttribute()->getAttributeCode());
        if ($this->getAttribute()->isValueEmpty($value)) {
            if ( !(is_array($value) && count($value)>0) ) {
                return false;
            }
        }
    }
    if ($this->getAttribute()->getIsUnique()) {
        if (!$this->getAttribute()->getEntity()->checkAttributeUniqueValue($this->getAttribute(), $object)) {
            $label = $this->getAttribute()->getFrontend()->getLabel();
            Mage::throwException(Mage::helper('eav')->__('The value of attribute "%s" must be unique.', $label));
        }
    }

    return true;
}

    public function beforeSave($object)
{
    $attrCode = $this->getAttribute()->getAttributeCode();
    $value = $object->getData($attrCode);
    if (!is_array($value) || !isset($value['images'])) {
        return;
    }

    if(!is_array($value['images']) && strlen($value['images']) > 0) {
        $value['images'] = Mage::helper('core')->jsonDecode($value['images']);
    }

    if (!is_array($value['images'])) {
        $value['images'] = array();
    }



    $clearImages = array();
    $newImages   = array();
    $existImages = array();
    if ($object->getIsDuplicate()!=true) {
        foreach ($value['images'] as &$image) {
            if(!empty($image['removed'])) {
                $clearImages[] = $image['file'];
            } else if (!isset($image['value_id'])) {
                $newFile                   = $this->_moveImageFromTmp($image['file']);
                $image['new_file'] = $newFile;
                $newImages[$image['file']] = $image;
                $this->_renamedImages[$image['file']] = $newFile;
                $image['file']             = $newFile;
            } else {
                $existImages[$image['file']] = $image;
            }
        }
    } else {
        // For duplicating we need copy original images.
        $duplicate = array();
        foreach ($value['images'] as &$image) {
            if (!isset($image['value_id'])) {
                continue;
            }
            $newFile = $this->_copyImage($image['file']);
            $newImages[$image['file']] = array(
                'new_file' => $newFile,
                'label' => $image['label'],
                'related_product_ids' => $image['related_product_ids'],
                'image_specific_url' => $image['image_specific_url'],
                'tag_ids' => $image['tag_ids']
            );
            $duplicate[$image['value_id']] = $newFile;
        }

        $value['duplicate'] = $duplicate;
    }

    foreach ($object->getMediaAttributes() as $mediaAttribute) {
        $mediaAttrCode = $mediaAttribute->getAttributeCode();
        $attrData = $object->getData($mediaAttrCode);

        if (in_array($attrData, $clearImages)) {
            $object->setData($mediaAttrCode, 'no_selection');
        }

        if (in_array($attrData, array_keys($newImages))) {
            $object->setData($mediaAttrCode, $newImages[$attrData]['new_file']);
            $object->setData($mediaAttrCode.'_label', $newImages[$attrData]['label']);
        }

        if (in_array($attrData, array_keys($existImages))) {
            $object->setData($mediaAttrCode.'_label', $existImages[$attrData]['label']);
        }
    }

    Mage::dispatchEvent('gallery_gallery_media_save_before', array('gallery' => $object, 'images' => $value));

    $object->setData($attrCode, $value);

    return $this;
}

    /**
     * Retrieve renamed image name
     *
     * @param string $file
     * @return string
     */
    public function getRenamedImage($file)
{
    if (isset($this->_renamedImages[$file])) {
        return $this->_renamedImages[$file];
    }

    return $file;
}

    public function afterSave($object)
{
    if ($object->getIsDuplicate() == true) {
        $this->duplicate($object);
        return;
    }

    $attrCode = $this->getAttribute()->getAttributeCode();
    $value = $object->getData($attrCode);
    if (!is_array($value) || !isset($value['images']) || $object->isLockedAttribute($attrCode)) {
        return;
    }

    $storeId = $object->getStoreId();

    $storeIds = $object->getStoreIds();
    $storeIds[] = Mage_Core_Model_App::ADMIN_STORE_ID;

    // remove current storeId
    $storeIds = array_flip($storeIds);
    unset($storeIds[$storeId]);
    $storeIds = array_keys($storeIds);


    $toDelete = array();
    $filesToValueIds = array();
    foreach ($value['images'] as &$image) {
        if(!empty($image['removed'])) {
            if(isset($image['value_id']) && !isset($picturesInOtherStores[$image['file']])) {
                $toDelete[] = $image['value_id'];
            }
            continue;
        }

        if(!isset($image['value_id'])) {
            $data = array();
            $data['entity_id']      = $object->getId();
            $data['attribute_id']   = $this->getAttribute()->getId();
            $data['value']          = $image['file'];
            $image['value_id']      = $this->_getResource()->insertGallery($data);
        }

        $this->_getResource()->deleteGalleryValueInStore($image['value_id'], $object->getStoreId());

        // Add per store labels, image_specific_url, position, disabled
        $data = array();
        $data['value_id']               = $image['value_id'];
        $data['label']                  = $image['label'];
        $data['related_product_ids']    = $image['related_product_ids'];
        $data['image_specific_url']     = $image['image_specific_url'];
        $data['tag_ids']                = $image['tag_ids'];
        $data['position']               = (int) $image['position'];
        $data['disabled']               = (int) $image['disabled'];
        $data['store_id']               = (int) $object->getStoreId();

        $this->_getResource()->insertGalleryValueInStore($data);
    }

    $this->_getResource()->deleteGallery($toDelete);
}

    /**
     * Add image to media gallery and return new filename
     *
     * @param Webtise_Gallery_Model_Gallery $gallery
     * @param string                     $file              file path of image in file system
     * @param string|array               $mediaAttribute    code of attribute with type 'media_image',
     *                                                      leave blank if image should be only in gallery
     * @param boolean                    $move              if true, it will move source file
     * @param boolean                    $exclude           mark image as disabled in gallery page view
     * @return string
     */
    public function addImage(Webtise_Gallery_Model_Gallery $gallery, $file,
                             $mediaAttribute = null, $move = false, $exclude = true)
{
    $file = realpath($file);

    if (!$file || !file_exists($file)) {
        Mage::throwException(Mage::helper('catalog')->__('Image does not exist.'));
    }

    Mage::dispatchEvent('gallery_gallery_media_add_image', array('gallery' => $gallery, 'image' => $file));

    $pathinfo = pathinfo($file);
    $imgExtensions = array('jpg','jpeg','gif','png');
    if (!isset($pathinfo['extension']) || !in_array(strtolower($pathinfo['extension']), $imgExtensions)) {
        Mage::throwException(Mage::helper('gallery')->__('Invalid image file type.'));
    }

    $fileName       = Mage_Core_Model_File_Uploader::getCorrectFileName($pathinfo['basename']);
    $dispretionPath = Mage_Core_Model_File_Uploader::getDispretionPath($fileName);
    $fileName       = $dispretionPath . DS . $fileName;

    $fileName = $this->_getNotDuplicatedFilename($fileName, $dispretionPath);

    $ioAdapter = new Varien_Io_File();
    $ioAdapter->setAllowCreateFolders(true);
    $distanationDirectory = dirname($this->_getConfig()->getTmpMediaPath($fileName));

    try {
        $ioAdapter->open(array(
            'path'=>$distanationDirectory
        ));

        /** @var $storageHelper Mage_Core_Helper_File_Storage_Database */
        $storageHelper = Mage::helper('core/file_storage_database');
        if ($move) {
            $ioAdapter->mv($file, $this->_getConfig()->getTmpMediaPath($fileName));

            //If this is used, filesystem should be configured properly
            $storageHelper->saveFile($this->_getConfig()->getTmpMediaShortUrl($fileName));
        } else {
            $ioAdapter->cp($file, $this->_getConfig()->getTmpMediaPath($fileName));

            $storageHelper->saveFile($this->_getConfig()->getTmpMediaShortUrl($fileName));
            $ioAdapter->chmod($this->_getConfig()->getTmpMediaPath($fileName), 0777);
        }
    }
    catch (Exception $e) {
        Mage::throwException(Mage::helper('catalog')->__('Failed to move file: %s', $e->getMessage()));
    }

    $fileName = str_replace(DS, '/', $fileName);

    $attrCode = $this->getAttribute()->getAttributeCode();
    $mediaGalleryData = $gallery->getData($attrCode);
    $position = 0;
    if (!is_array($mediaGalleryData)) {
        $mediaGalleryData = array(
            'images' => array()
        );
    }

    foreach ($mediaGalleryData['images'] as &$image) {
        if (isset($image['position']) && $image['position'] > $position) {
            $position = $image['position'];
        }
    }

    $position++;
    $mediaGalleryData['images'][] = array(
        'file'                  => $fileName,
        'related_product_ids'   => '',
        'image_specific_url'    => '',
        'tag_ids'               => '',
        'position'              => $position,
        'label'                 => '',
        'disabled'              => (int) $exclude
    );

    $gallery->setData($attrCode, $mediaGalleryData);

    if (!is_null($mediaAttribute)) {
        $this->setMediaAttribute($gallery, $mediaAttribute, $fileName);
    }

    return $fileName;
}

    /**
     * Add images with different media attributes.
     * Image will be added only once if the same image is used with different media attributes
     *
     * @param Webtise_Gallery_Model_Gallery $gallery
     * @param array $fileAndAttributesArray array of arrays of filename and corresponding media attribute
     * @param string $filePath path, where image cand be found
     * @param boolean $move if true, it will move source file
     * @param boolean $exclude mark image as disabled in gallery page view
     * @return array array of parallel arrays with original and renamed files
     */
    public function addImagesWithDifferentMediaAttributes(Webtise_Gallery_Model_Gallery $gallery,
                                                          $fileAndAttributesArray, $filePath = '', $move = false, $exclude = true) {

    $alreadyAddedFiles = array();
    $alreadyAddedFilesNames = array();

    foreach ($fileAndAttributesArray as $key => $value) {
        $keyInAddedFiles = array_search($value['file'], $alreadyAddedFiles, true);
        if ($keyInAddedFiles === false) {
            $savedFileName = $this->addImage($gallery, $filePath . $value['file'], null, $move, $exclude);
            $alreadyAddedFiles[$key] = $value['file'];
            $alreadyAddedFilesNames[$key] = $savedFileName;
        } else {
            $savedFileName = $alreadyAddedFilesNames[$keyInAddedFiles];
        }

        if (!is_null($value['mediaAttribute'])) {
            $this->setMediaAttribute($gallery, $value['mediaAttribute'], $savedFileName);
        }

    }

    return array('alreadyAddedFiles' => $alreadyAddedFiles, 'alreadyAddedFilesNames' => $alreadyAddedFilesNames);
}

    /**
     * Update image in gallery
     *
     * @param Webtise_Gallery_Model_Gallery $gallery
     * @param sting $file
     * @param array $data
     * @return Mage_Catalog_Model_Gallery_Attribute_Backend_Media
     */
    public function updateImage(Webtise_Gallery_Model_Gallery $gallery, $file, $data)
{
    $fieldsMap = array(
        'label'                 => 'label',
        'related_product_ids'   => 'related_product_ids',
        'image_specific_url'    => 'image_specific_url',
        'tag_ids'               => 'tag_ids',
        'position'              => 'position',
        'disabled'              => 'disabled',
        'exclude'               => 'disabled'
    );

    $attrCode = $this->getAttribute()->getAttributeCode();

    $mediaGalleryData = $gallery->getData($attrCode);

    if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
        return $this;
    }

    foreach ($mediaGalleryData['images'] as &$image) {
        if ($image['file'] == $file) {
            foreach ($fieldsMap as $mappedField=>$realField) {
                if (isset($data[$mappedField])) {
                    $image[$realField] = $data[$mappedField];
                }
            }
        }
    }

    $gallery->setData($attrCode, $mediaGalleryData);
    return $this;
}

    /**
     * Remove image from gallery
     *
     * @param Webtise_Gallery_Model_Gallery $gallery
     * @param string $file
     * @return Webtise_Gallery_Model_Gallery_Attribute_Backend_Media
     */
    public function removeImage(Webtise_Gallery_Model_Gallery $gallery, $file)
{
    $attrCode = $this->getAttribute()->getAttributeCode();

    $mediaGalleryData = $gallery->getData($attrCode);

    if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
        return $this;
    }

    foreach ($mediaGalleryData['images'] as &$image) {
        if ($image['file'] == $file) {
            $image['removed'] = 1;
        }
    }

    $gallery->setData($attrCode, $mediaGalleryData);

    return $this;
}

    /**
     * Retrive image from gallery
     *
     * @param Webtise_Gallery_Model_Gallery $gallery
     * @param string $file
     * @return array|boolean
     */
    public function getImage(Webtise_Gallery_Model_Gallery $gallery, $file)
{
    $attrCode = $this->getAttribute()->getAttributeCode();
    $mediaGalleryData = $gallery->getData($attrCode);
    if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
        return false;
    }

    foreach ($mediaGalleryData['images'] as $image) {
        if ($image['file'] == $file) {
            return $image;
        }
    }

    return false;
}

    /**
     * Clear media attribute value
     *
     * @param Webtise_Gallery_Model_Gallery $gallery
     * @param string|array $mediaAttribute
     * @return Webtise_Gallery_Model_Gallery_Attribute_Backend_Media
     */
    public function clearMediaAttribute(Webtise_Gallery_Model_Gallery $gallery, $mediaAttribute)
{
    $mediaAttributeCodes = array_keys($gallery->getMediaAttributes());

    if (is_array($mediaAttribute)) {
        foreach ($mediaAttribute as $attribute) {
            if (in_array($attribute, $mediaAttributeCodes)) {
                $gallery->setData($attribute, null);
            }
        }
    } elseif (in_array($mediaAttribute, $mediaAttributeCodes)) {
        $gallery->setData($mediaAttribute, null);
    }

    return $this;
}

    /**
     * Set media attribute value
     *
     * @param Webtise_Gallery_Model_Gallery $gallery
     * @param string|array $mediaAttribute
     * @param string $value
     * @return Webtise_Gallery_Model_Gallery_Attribute_Backend_Media
     */
    public function setMediaAttribute(Webtise_Gallery_Model_Gallery $gallery, $mediaAttribute, $value)
{
    $mediaAttributeCodes = array_keys($gallery->getMediaAttributes());

    if (is_array($mediaAttribute)) {
        foreach ($mediaAttribute as $atttribute) {
            if (in_array($atttribute, $mediaAttributeCodes)) {
                $gallery->setData($atttribute, $value);
            }
        }
    } elseif (in_array($mediaAttribute, $mediaAttributeCodes)) {
        $gallery->setData($mediaAttribute, $value);
    }

    return $this;
}

    /**
     * Retrieve resource model
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Gallery_Attribute_Backend_Media
     */
    protected function _getResource()
{
    return Mage::getResourceSingleton('gallery/gallery_attribute_backend_media');
}

    /**
     * Retrive media config
     *
     * @return Mage_Catalog_Model_Gallery_Media_Config
     */
    protected function _getConfig()
{
    return Mage::getSingleton('gallery/gallery_media_config');
}

    /**
     * Move image from temporary directory to normal
     *
     * @param string $file
     * @return string
     */
    protected function _moveImageFromTmp($file)
{
    $ioObject = new Varien_Io_File();
    $destDirectory = dirname($this->_getConfig()->getMediaPath($file));
    try {
        $ioObject->open(array('path'=>$destDirectory));
    } catch (Exception $e) {
        $ioObject->mkdir($destDirectory, 0777, true);
        $ioObject->open(array('path'=>$destDirectory));
    }

    if (strrpos($file, '.tmp') == strlen($file)-4) {
        $file = substr($file, 0, strlen($file)-4);
    }
    $destFile = $this->_getUniqueFileName($file, $ioObject->dirsep());

    /** @var $storageHelper Mage_Core_Helper_File_Storage_Database */
    $storageHelper = Mage::helper('core/file_storage_database');

    if ($storageHelper->checkDbUsage()) {
        $storageHelper->renameFile(
            $this->_getConfig()->getTmpMediaShortUrl($file),
            $this->_getConfig()->getMediaShortUrl($destFile));

        $ioObject->rm($this->_getConfig()->getTmpMediaPath($file));
        $ioObject->rm($this->_getConfig()->getMediaPath($destFile));
    } else {
        $ioObject->mv(
            $this->_getConfig()->getTmpMediaPath($file),
            $this->_getConfig()->getMediaPath($destFile)
        );
    }

    return str_replace($ioObject->dirsep(), '/', $destFile);
}

    /**
     * Check whether file to move exists. Getting unique name
     *
     * @param <type> $file
     * @param <type> $dirsep
     * @return string
     */
    protected function _getUniqueFileName($file, $dirsep) {
    if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
        $destFile = Mage::helper('core/file_storage_database')
            ->getUniqueFilename(
                Mage::getSingleton('gallery/gallery_media_config')->getBaseMediaUrlAddition(),
                $file
            );
    } else {
        $destFile = dirname($file) . $dirsep
            . Mage_Core_Model_File_Uploader::getNewFileName($this->_getConfig()->getMediaPath($file));
    }

    return $destFile;
}

    /**
     * Copy image and return new filename.
     *
     * @param string $file
     * @return string
     */
    protected function _copyImage($file)
{
    try {
        $ioObject = new Varien_Io_File();
        $destDirectory = dirname($this->_getConfig()->getMediaPath($file));
        $ioObject->open(array('path'=>$destDirectory));

        $destFile = $this->_getUniqueFileName($file, $ioObject->dirsep());

        if (!$ioObject->fileExists($this->_getConfig()->getMediaPath($file),true)) {
            throw new Exception();
        }

        if (Mage::helper('core/file_storage_database')->checkDbUsage()) {
            Mage::helper('core/file_storage_database')
                ->copyFile($this->_getConfig()->getMediaShortUrl($file),
                    $this->_getConfig()->getMediaShortUrl($destFile));

            $ioObject->rm($this->_getConfig()->getMediaPath($destFile));
        } else {
            $ioObject->cp(
                $this->_getConfig()->getMediaPath($file),
                $this->_getConfig()->getMediaPath($destFile)
            );
        }

    } catch (Exception $e) {
        $file = $this->_getConfig()->getMediaPath($file);
        Mage::throwException(
            Mage::helper('gallery')->__('Failed to copy file %s. Please, delete media with non-existing images and try again.', $file)
        );
    }

    return str_replace($ioObject->dirsep(), '/', $destFile);
}

    public function duplicate($object)
{
    $attrCode = $this->getAttribute()->getAttributeCode();
    $mediaGalleryData = $object->getData($attrCode);

    if (!isset($mediaGalleryData['images']) || !is_array($mediaGalleryData['images'])) {
        return $this;
    }

    $this->_getResource()->duplicate(
        $this,
        (isset($mediaGalleryData['duplicate']) ? $mediaGalleryData['duplicate'] : array()),
        $object->getOriginalId(),
        $object->getId()
    );

    return $this;
}

    /**
     * Get filename which is not duplicated with other files in media temporary and media directories
     *
     * @param String $fileName
     * @param String $dispretionPath
     * @return String
     */
    protected function _getNotDuplicatedFilename($fileName, $dispretionPath)
{
    $fileMediaName = $dispretionPath . DS
        . Mage_Core_Model_File_Uploader::getNewFileName($this->_getConfig()->getMediaPath($fileName));
    $fileTmpMediaName = $dispretionPath . DS
        . Mage_Core_Model_File_Uploader::getNewFileName($this->_getConfig()->getTmpMediaPath($fileName));

    if ($fileMediaName != $fileTmpMediaName) {
        if ($fileMediaName != $fileName) {
            return $this->_getNotDuplicatedFileName($fileMediaName, $dispretionPath);
        } elseif ($fileTmpMediaName != $fileName) {
            return $this->_getNotDuplicatedFilename($fileTmpMediaName, $dispretionPath);
        }
    }

    return $fileMediaName;
}
}
