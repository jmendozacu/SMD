<?php
/**
 * Created by PhpStorm.
 * User: joshcarter
 * Date: 16/06/2016
 * Time: 11:05
 */

class Webtise_Gallery_Block_Adminhtml_Gallery_Helper_Gallery_Content extends Mage_Adminhtml_Block_Widget
{
    /**
     * Type of uploader block
     *
     * @var string
     */
    protected $_uploaderType = 'uploader/multiple';

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('webtise/gallery/helper/gallery.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('uploader',
            $this->getLayout()->createBlock($this->_uploaderType)
        );

        $this->getUploader()->getUploaderConfig()
            ->setFileParameterName('image')
            ->setTarget(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/webtise_gallery_gallery/upload'));

        $browseConfig = $this->getUploader()->getButtonConfig();
        $browseConfig
            ->setAttributes(array(
                'accept' => $browseConfig->getMimeTypesByExtensions('gif, png, jpeg, jpg')
            ));

        Mage::dispatchEvent('webtise_gallery_gallery_prepare_layout', array('block' => $this));

        return parent::_prepareLayout();
    }

    /**
     * Retrive uploader block
     *
     * @return Mage_Adminhtml_Block_Media_Uploader
     */
    public function getUploader()
    {
        return $this->getChild('uploader');
    }

    /**
     * Retrive uploader block html
     *
     * @return string
     */
    public function getUploaderHtml()
    {
        return $this->getChildHtml('uploader');
    }

    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    public function getAddImagesButton()
    {
        return $this->getButtonHtml(
            Mage::helper('catalog')->__('Add New Images'),
            $this->getJsObjectName() . '.showUploader()',
            'add',
            $this->getHtmlId() . '_add_images_button'
        );
    }

    public function getImagesJson()
    {
        if(is_array($this->getElement()->getValue())) {
            $value = $this->getElement()->getValue();
            if(count($value['images']) > 0) {
                foreach ($value['images'] as &$image) {
                    $image['url'] = Mage::getSingleton('gallery/gallery_media_config')
                        ->getMediaUrl($image['file']);
                }
                return Mage::helper('core')->jsonEncode($value['images']);
            }
        }
        return '[]';
    }

    public function getImagesValuesJson()
    {
        $values = array();
        foreach ($this->getMediaAttributes() as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            $values[$attribute->getAttributeCode()] = $this->getElement()->getDataObject()->getData(
                $attribute->getAttributeCode()
            );
        }
        return Mage::helper('core')->jsonEncode($values);
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getImageTypes()
    {
        $imageTypes = array();
        foreach ($this->getMediaAttributes() as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute */
            $imageTypes[$attribute->getAttributeCode()] = array(
                'label' => $attribute->getFrontend()->getLabel() . ' '
                    . Mage::helper('catalog')->__($this->getElement()->getScopeLabel($attribute)),
                'field' => $this->getElement()->getAttributeFieldName($attribute)
            );
        }
        return $imageTypes;
    }

    public function hasUseDefault()
    {
        foreach ($this->getMediaAttributes() as $attribute) {
            return true;
        }

        return false;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getMediaAttributes()
    {
        return $this->getElement()->getDataObject()->getMediaAttributes();
    }

    public function getImageTypesJson()
    {
        return Mage::helper('core')->jsonEncode($this->getImageTypes());
    }

    /**
     * Return Collection of tags if they exist
     *
     * @return bool
     */
    public function getTags()
    {
        $tags = Mage::getModel('gallery/gallery_tag')->getCollection()->addFieldToFilter('status', 1);
        if($tags->getSize() > 0) {
            return $tags;
        }
        return false;
    }
}