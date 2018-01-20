<?php
/**
 * Blackbird ContentManager Module
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@bird.eu so we can send you a copy immediately.
 *
 * @category	Blackbird
 * @package		Blackbird_ContentManager
 * @copyright	Copyright (c) 2014 Blackbird Content Manager (http://black.bird.eu)
 * @author		Blackbird Team
 * @license		http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @version		
 */

class Blackbird_ContentManager_Model_Contenttype extends Mage_Core_Model_Abstract
{
    protected $_canAffectOptions = false;
    
    /**
     * ContentType object customization (not stored in DB)
     *
     * @var array
     */
    protected $_customOptions = array();
    protected $_optionInstance;
    protected $_options = array();
    
    /**
     * ContentType type instance
     *
     * @var Blackbird_ContentManager_Model_ContentType_Type_Abstract
     */
    protected $_typeInstance            = null;
    
    /**
     * ContentType type instance as singleton
     */
    protected $_typeInstanceSingleton   = null;
    
    /**
     * ContentType type grid attributes
     */
    protected $_gridAttributes   = null;    
    
	public function _construct(){
		parent::_construct();
		$this->_init('contentmanager/contenttype');
	}
	
	protected function _beforeSave()
	{
	 /*   $this->setTypeHasOptions(false);
	    $this->setTypeHasRequiredOptions(false);
	
	    //$this->getTypeInstance(true)->beforeSave($this);
	*/
	    $hasOptions         = false;
	    $hasRequiredOptions = false;
	
	    /**
	     * $this->_canAffectOptions - set by type instance only
	     * $this->getCanSaveCustomOptions() - set either in controller when "Custom Options" ajax tab is loaded,
	     * or in type instance as well
	     */
	   
	    if ($this->getCanSaveCustomOptions()) {
	        $options = $this->getContentTypeOptions();
	        if (is_array($options)) {
	            $this->setIsCustomOptionChanged(true);
	            foreach ($this->getContentTypeOptions() as $option) {
	                $this->getOptionInstance()->addOption($option);
	                if ((!isset($option['is_delete'])) || $option['is_delete'] != '1') {
	                    $hasOptions = true;
	                }
	            }
	            foreach ($this->getOptionInstance()->getOptions() as $option) {
	                if ($option['is_require'] == '1') {
	                    $hasRequiredOptions = true;
	                    break;
	                }
	            }
	        }
	    }
	
	    /**
	     * Set true, if any
	     * Set false, ONLY if options have been affected by Options tab
	     */
        $this->setHasOptions($hasOptions);
	    $this->setRequiredOptions($hasRequiredOptions);

	    parent::_beforeSave();
	}
	
	/**
	 * Check/set if options can be affected when saving contenttype
	 * If value specified, it will be set.
	 *
	 * @param   bool $value
	 * @return  bool
	 */
	public function canAffectOptions($value = null)
	{
	    return true;
	    
	    /*
	    if (null !== $value) {
	        $this->_canAffectOptions = (bool)$value;
	    }
	    return $this->_canAffectOptions;*/
	}
	
	protected function _afterSave()
	{
	    /**
	     * CustomContentType Options
	    */
	    $this->getOptionInstance()->setContentType($this)
	    ->saveOptions();
	
	    $result = parent::_afterSave();
	    return $result;
	}
	
	/**
	 * Load contenttype options if they exists
	 *
	 * @return Blackbird_ContentManager_Model_ContentType
	 */
	protected function _afterLoad()
	{
	    parent::_afterLoad();
            
	    /**
	     * Load contenttype options
	    */
	    //if ($this->getHasOptions()) {    @todo 20130327 - Blackbird - Return always false...
	        foreach ($this->getContentTypeOptionsCollection() as $option) {
	            $option->setContentType($this);
	            $this->addOption($option);
	        }
	    //}
	    return $this;
	}
	
	/**
	 * Retrieve option instance
	 *
	 * @return Blackbird_ContentManager_Model_ContentType_Option
	 */
	public function getOptionInstance()
	{
	    if (!$this->_optionInstance) {
	        $this->_optionInstance = Mage::getSingleton('contentmanager/contenttype_option');
	    }
	    return $this->_optionInstance;
	}
	
	/**
	 * Retrieve options collection of contenttype
	 *
	 * @return Blackbird_ContentManager_Model_Resource_Eav_Mysql4_ContentType_Option_Collection
	 */
	public function getContentTypeOptionsCollection()
	{
	    $collection = $this->getOptionInstance()
	    ->getContentTypeOptionCollection($this);
	
	    return $collection;
	}
	
	/**
	 * Add option to array of contenttype options
	 *
	 * @param Blackbird_ContentManager_Model_ContentType_Option $option
	 * @return Blackbird_ContentManager_Model_ContentType
	 */
	public function addOption(Blackbird_ContentManager_Model_ContentType_Option $option)
	{
	    $this->_options[$option->getId()] = $option;
	    return $this;
	}
	
	/**
	 * Get option from options array of contenttype by given option id
	 *
	 * @param int $optionId
	 * @return Blackbird_ContentManager_Model_ContentType_Option | null
	 */
	public function getOptionById($optionId)
	{
	    if (isset($this->_options[$optionId])) {
	        return $this->_options[$optionId];
	    }
	
	    return null;
	}
	
	/**
	 * Get all options of contenttype
	 *
	 * @return array
	 */
	public function getOptions()
	{
	    return $this->_options;
	}
        
        /**
         * Return content type array
         */
        public function getOptionArray()
        {
            $contenTypes = Mage::getModel('contentmanager/contenttype')
                    ->getCollection()
                    ->addFieldToSelect('title')
                    ->addFieldToSelect('ct_id');
            
            $result = array();
            foreach($contenTypes as $contentType)
            {
                $result[$contentType->getCtId()] = $contentType->getTitle();
            }
            
            return $result;
        }
        
        /**
         * Get grid attributes
         */
        public function getGridAttributes()
        {
            if($this->_gridAttributes == null)
            {
                //load all fields for this contenttype that are displayed in the grid
                $fields = Mage::getModel('contentmanager/contenttype_option')
                            ->getCollection()
                            ->addFieldToFilter('show_in_grid', 1)
                            ->addFieldToFilter('ct_id', $this->getId())
                            ->addTitleToResult($this->getStoreId());

                $fieldsToSelect = array();
                foreach($fields as $field)
                {
                    $fieldsToSelect[] = $field->getIdentifier();
                }
                
                $this->_gridAttributes = $fieldsToSelect;
            }
            
            return $this->_gridAttributes;
        }        
}