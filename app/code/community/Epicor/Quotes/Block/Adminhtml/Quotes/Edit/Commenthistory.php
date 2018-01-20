<?php

class Epicor_Quotes_Block_Adminhtml_Quotes_Edit_Commenthistory extends Epicor_Quotes_Block_Adminhtml_Quotes_Edit_Abstract
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

    public function getPublishNoteUrl($note)
    {
        return Mage::helper("adminhtml")->getUrl(
            "adminhtml/quotes_quotes/publishnote/",
            array('id' => $note->getId())
        );
    }

    public function getNewNoteUrl()
    {
        return Mage::helper("adminhtml")->getUrl(
            "adminhtml/quotes_quotes/submitnewnote/",
            array('id' => $this->getQuote()->getId())
        );
    }

    public function getCommentStateUrl($note)
    {
        return Mage::helper("adminhtml")->getUrl(
            "adminhtml/quotes_quotes/changenotestate/",
            array('id' => $note->getId())
        );
    }

}
