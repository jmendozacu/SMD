<?php

class Epicor_Quotes_Block_View_Commenthistory extends Epicor_Quotes_Block_View_Abstract
{

    /**
     * 
     * @param Epicor_Quotes_Model_Quote_Note $note
     * @return Mage_Admin_Model_User|Mage_Customer_Model_Customer
     */
    public function getNoteUser($note)
    {
        if ($note->getIsFormatted()) {
            $userInfo = $note;
        } else if ($note->isAdminNote()) {
            $userInfo = $note->getAdmin();
        } else {
            $userInfo = $this->getQuote()->getCustomer();
        }

        return $userInfo;
    }

    public function getNewNoteUrl() {
        return $this->getUrl('epicor_quotes/manage/newnote', array('id' => $this->getQuote()->getId()));
    }
}
