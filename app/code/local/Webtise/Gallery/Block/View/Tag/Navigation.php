<?php
/**
 * @author joshuacarter <josh@interjar.com>
 * @category Interjar
 * @package Interjar_*PACKAGE NAME*
 */
class Webtise_Gallery_Block_View_Tag_Navigation extends Mage_Core_Block_Template
{
    /** @var array $_tags Array of Tag Ids */
    protected $_tags = [];

    /** @var array $_categories Array of Tag Category Ids */
    protected $_categories = [];

    /** @var array $_query Array of Tag Querys for URL */
    protected $_query = [];

    /**
     * Webtise_Gallery_Block_View_Tag_Navigation constructor
     */
    public function __construct()
    {
        parent::__construct();
        if(!$this->getTemplate()) {
            $this->setTemplate('webtise/gallery/view/tag/navigation.phtml');
        }
        $this->setQuery();
    }

    /**
     * Set Galleries to Block
     *
     * @param $galleries
     */
    public function setGalleries($galleries)
    {
        $this->setData('galleries', $galleries);
    }

    /**
     * Set Query to class variable for use in other functions
     */
    public function setQuery()
    {
        $query = $this->getData('query');
        if($query && count($query) > 0) {
            $this->_query = $query;
        }
    }

    /**
     * Return Galleries if set
     *
     * @return bool|mixed
     */
    public function getGalleries()
    {
        if($galleries = $this->getData('galleries')) {
            return $galleries;
        }
        return false;
    }

    /**
     * Return Collection of Tag Categories Present on the page
     *
     * @return mixed
     */
    public function getTagCategories()
    {
        if(!empty($this->_tags)) {
            $tags = $this->_tags;
        }else {
            $tags = $this->getTags();
        }
        $categories = Mage::getModel('gallery/gallery_tag_category')->getCollection()
            ->addFieldToFilter('status', 1);
        foreach($categories as $category) {
            $catTags = explode(',', $category->getTagIds());
            $matched = array_intersect(array_keys($tags), $catTags);
            if(count($matched) > 0) {
                $this->_categories[] = $category->getId();
            }
        }
        $categories->addFieldToFilter('entity_id', array('in' => $this->_categories));
        return $categories;
    }

    public function getTags()
    {
        $galleries = $this->getGalleries();
        if($galleries) {
            foreach($galleries as $gallery) {
                $attribute = $gallery->getResource()->getAttribute('media_gallery');
                $attribute->getBackend()->afterLoad($gallery);
                $_media = $gallery->getMediaGalleryImages();
                foreach($_media as $image) {
                    $tagIds = explode(',', $image->getTagIds());
                    if(count($tagIds) > 0) {
                        foreach ($tagIds as $tagId) {
                            if (isset($this->_tags[$tagId])) {
                                $this->_tags[$tagId] = ($this->_tags[$tagId] + 1);
                            } else {
                                $this->_tags[$tagId] = 1;
                            }
                        }
                    }
                }
            }
        }
        return $this->_tags;
    }

    /**
     * Return Collection of Tags present on page for said category
     *
     * @param $category
     * @return mixed
     */
    public function getCategoryTags($category)
    {
        if(!empty($this->_tags)) {
            $tags = $this->_tags;
        }else {
            $tags = $this->getTags();
        }
        $tagIds = [];
        $catTagsIds = explode(',', $category->getTagIds());
        foreach($catTagsIds as $catTag) {
            if(in_array($catTag, array_keys($tags))) {
                $tagIds[] = $catTag;
            }
        }
         return Mage::getModel('gallery/gallery_tag')->getCollection()
             ->addFieldToFilter('entity_id', array('in' => $tagIds))
             ->addFieldToFilter('status', 1);
    }

    /**
     * Return count of images for given tag
     *
     * @param $tag
     * @return int
     */
    public function getTagImageCount($tag)
    {
        return (isset($this->_tags[$tag->getId()])) ? $this->_tags[$tag->getId()] : 0;
    }

    /**
     * Return bool on whether category is swatch category
     *
     * @param $category
     * @return bool
     */
    public function isSwatchCategory($category)
    {
        switch($category->getFrontendDisplay()) {
            case 'swatch':
                return true;
                break;
            case 'text_swatch':
                return true;
                break;
            default:
                return false;
        }
    }

    /**
     * Return URL for Image of Tag
     *
     * @param $tag
     * @return string
     */
    public function getSwatchImg($tag)
    {
        return Mage::getBaseUrl('media') . $tag->getImage();
    }
}