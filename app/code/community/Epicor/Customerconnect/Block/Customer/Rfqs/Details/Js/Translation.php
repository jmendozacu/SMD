<?php

/**
 * RFQ details js block
 * 
 * @category   Epicor
 * @package    Epicor_Customerconnect
 * @author     Epicor Websales Team
 */

class Epicor_Customerconnect_Block_Customer_Rfqs_Details_Js_Translation extends Epicor_Common_Block_Js_Translation
{

    protected function _construct()
    {
        parent::_construct();
        
        $translations = array(
            /* couldn't find these in a js file, but where there before so I kept them */
            'You must supply at least one Contact' => $this->__('You must supply at least one Contact'),
            'You must supply at least one Line' => $this->__('You must supply at least one Line'),
            'One or more lines require configuration, please see lines with a "Configure" link' => $this->__('One or more lines require configuration, please see lines with a "Configure" link'),
            'Are you sure you want to delete?' => $this->__('Are you sure you want to delete?'),
            'That function is not available' => $this->__('That function is not available'),
            'Please enter a qty' => $this->__('Please enter a qty'),
            'Line added successfully' => $this->__('Line added successfully'),
            'Not currently available' => $this->__('Not currently available'),
            /* skin/frontend/base/default/epicor/common/js/common.js */
            'Error occured in Ajax Call' => $this->__('Error occured in Ajax Call'),
            'No records found.' => $this->__('No records found.'),

            /* skin/frontend/base/default/epicor/comm/js/quickadd.js */
            'Error occured retrieving additional data' => $this->__('Error occured retrieving additional data'),
            
            /* skin/frontend/base/default/epicor/customerconnect/js/rfq/details/contacts.js */
            'Are you sure you want to delete selected contact?' => $this->__('Are you sure you want to delete selected contact?'),
            'No Contacts Available' => $this->__('No Contacts Available'),

            /* skin/frontend/base/default/epicor/customerconnect/js/rfq/details/core.js */
            'There are unsaved changes to this quote. These changes will be lost. Are you sure you wish to continue?' => $this->__('There are unsaved changes to this quote. These changes will be lost. Are you sure you wish to continue?'),
            'One or more options is incorrect, please see page for details' => $this->__('One or more options is incorrect, please see page for details'),
            'Are you sure you want to delete selected line?' => $this->__('Are you sure you want to delete selected line?'),

            /* skin/frontend/base/default/epicor/customerconnect/js/rfq/details/lines.js */
            'Please select one or more lines' => $this->__('Please select one or more lines'),
            'One or more lines had errors:' => $this->__('One or more lines had errors:'),
            'SKU' => $this->__('SKU'),
            'Does not exist - Select Custom Part' => $this->__('Does not exist - Select Custom Part'),
            'Does not exist' => $this->__('Does not exist'),
            'Lines added successfully' => $this->__('Lines added successfully'),
            'One or more products require configuration. Please click on each "Configure" link in the lines list' => $this->__('One or more products require configuration. Please click on each "Configure" link in the lines list'),
            'You must provide an SKU for all non-custom parts' => $this->__('You must provide an SKU for all non-custom parts'),
            'You must provide a name for all custom parts' => $this->__('You must provide a name for all custom parts'),
            'All quantities must be valid' => $this->__('All quantities must be valid'),
            'Configure' => $this->__('Configure'),
            'Edit Configuration' => $this->__('Edit Configuration'),
            'No lines added' => $this->__('No lines added'),
            'Line(s) added successfully' => $this->__('Line(s) added successfully'),
            'Are you sure you want to delete selected lines?' => $this->__('Are you sure you want to delete selected lines?'),
            'Are you sure you want to clone selected lines?' => $this->__('Are you sure you want to clone selected lines?'),
            'TBA' => $this->__('TBA'),
            
            /* skin/frontend/base/default/epicor/customerconnect/js/rfq/details/salesreps.js */
            'Are you sure you want to delete selected sales rep?' => $this->__('Are you sure you want to delete selected sales rep?'),

            /* skin/frontend/base/default/epicor/salesrep/js/salesrepPricing.js */
            'The price entered was too low' => $this->__('The price entered was too low'),
            'The discount entered was too high' => $this->__('The discount entered was too high'),
            
            /* skin/frontend/base/default/epicor/salesrep/js/rfq-extra.js */
            'Revert to Web Price' => $this->__('Revert to Web Price'),
        );
        
        $this->setTranslations($translations);
    }

}
