<?php

/**
 * Callout block
 * 
 * @method setImage($value)
 * @method getImage()
 * @method setImageAlt($value)
 * @method getImageAlt()
 * @method setLink($value)
 * @method getLink()
 * @method setSku($value)
 * @method getSku()
 * @method setProduct($value)
 * @method getProduct()
 * @method setHtml($value)
 * @method getHtml()
 * @method setType($value)
 * @method getType()
 * @method setTitle($value)
 * @method getTitle()
 * 
 */
class Epicor_FlexiTheme_Block_Frontend_Template_Callout extends Mage_Catalog_Block_Product_Abstract {

    /**
     * Loads the block model and sets up variables required
     * 
     * @param integer $block_id 
     */
    public function setBlockId($block_id) {

        $block = Mage::getModel('flexitheme/layout_block')->load(base64_decode($block_id), 'block_name');

        if ($block) {
            $data = unserialize($block->getBlockExtra());

            $this->setModelId($block->getId());
            
            $this->setType($data['type']);
            $this->setLink($data['url']);
            $this->setImage($data['image']);
            $this->setImageAlt($data['image_alt']);
            $this->setSku($data['product_sku']);
            $product = Mage::getModel('catalog/product');
            /* @var $product Epicor_Comm_Model_Product */
            $this->setProduct($product->load($product->getIdBySku($this->getSku())));
            $this->setHtml(Mage::helper('cms')->getBlockTemplateProcessor()->filter($data['html']));
            
            $title = $data['title'];

            
            switch ($data['type']) {
                case 'featured_product':
                    if (empty($title)) {
                        $title = $this->getProduct()->getName();
                    }
                    break;
            }

            $this->setTitle($title);
            
        }
    }
    
    public function _toHtml()
    {
        if($this->getType() == 'featured_product') {
            Mage::dispatchEvent('featured_product_display',array('product' => $this->getProduct(), 'block' => $this));
        }
        
        return parent::_toHtml();
    }

}