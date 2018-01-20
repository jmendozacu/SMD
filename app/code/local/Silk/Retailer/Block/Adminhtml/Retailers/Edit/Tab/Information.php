<?php

/**
 * Copyright (c) 2016, SILK Software
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *    This product includes software developed by the SILK Software.
 * 4. Neither the name of the SILK Software nor the
 *   names of its contributors may be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY SILK Software ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL SILK Software BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * Created by PhpStorm.
 * User: Bob song <bob.song@silksoftware.com>
 * Date: 17-3-1
 * Time: 15:03
 */
class Silk_Retailer_Block_Adminhtml_Retailers_Edit_Tab_Information extends Mage_Adminhtml_Block_Widget_Form
{
	public function getFormHtml()
	{
		return parent::getFormHtml();
	}

	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();
		$this->setForm($form);
		if ($id = $this->getRequest()->getParam('id')) {
			$formData = Mage::getModel('silk_retailer/retailer')->load($id);
			$details = $form->addFieldset(
				'retailer_information_form',
				array(
					'legend' => $this->__('Edit Retailer Information')
				)
			);
		} else {
			$formData = Mage::getModel('silk_retailer/retailer');
			$details = $form->addFieldset(
				'retailer_information_form',
				array(
					'legend' => $this->__('New Retailer Information')
				)
			);
		}

		$details->addField(
				'retailer_id', 'hidden',
				array(
						'name'  => 'Reatiler Id',
						'label' => $this->__('Reatiler Id'),
						'required' => false
				)
		);

		$details->addField(
			'title', 'text',
			array(
				'name'  => 'title',
				'label' => $this->__('Title'),
				'required' => false
			)
		);

		$details->addField(
			'address1', 'text',
			array(
				'name'  => 'address1',
				'label' => $this->__('Address1'),
				'required' => true
			)
		);

		$details->addField(
			'address2', 'text',
			array(
				'name'  => 'address2',
				'label' => $this->__('Address2'),
				'required' => false
			)
		);

		$details->addField(
			'town', 'text',
			array(
				'name'  => 'town',
				'label' => $this->__('Town'),
				'required' => true
			)
		);

		$details->addField(
			'email', 'text',
			array(
				'name'  => 'email',
				'label' => $this->__('Email'),
				'required' => true
			)
		);

		$details->addField(
			'city', 'text',
			array(
				'name'  => 'city',
				'label' => $this->__('City'),
				'required' => true
			)
		);

		$details->addField(
			'country', 'select',
			array(
				'name'  => 'country',
				'label' => $this->__('Country'),
				'options'	=>	array('1' => ''),
				'required' => true
			)
		)
		->setValues(
			Mage::getSingleton('directory/country')->getResourceCollection()
				->loadByStore()
				->toOptionArray()
		);

		$details->addField(
			'state', 'text',
			array(
				'name'  => 'state',
				'label' => $this->__('State/Province'),
				'required' => true
			)
		);

		$details->addField(
			'zip', 'text',
			array(
				'name'  => 'zip',
				'label' => $this->__('Zip/Postal Code'),
				'required' => true
			)
		);

		$details->addField(
			'telephone', 'text',
			array(
				'name'  => 'telephone',
				'label' => $this->__('Phone'),
				'required' => true
			)
		);

		$details->addField(
			'latitude', 'text',
			array(
				'name'  => 'latitude',
				'label' => $this->__('Latitude'),
				'required' => true
			)
		);

		$details->addField(
			'longtitude', 'text',
			array(
				'name'  => 'longtitude',
				'label' => $this->__('Longtitude'),
				'required' => true
			)
		);
		/**
		$details->addField(
			'agent', 'select',
			array(
				'name'  => 'agent',
				'label' => $this->__('Agent'),
				'options'	=>	array('1' => 'Setphanie McAllister(Scotland)'),
				'required' => false
			)
		);*/
		$details->addField(
			'agent', 'text',
			array(
				'name'  => 'agent',
				'label' => $this->__('Agent'),
				'required' => false
			)
		);

		$details->addField(
			'stock', 'select',
			array(
				'name'  => 'stock',
				'label' => $this->__('Stock'),
				'options'	=>	array('1' => 'Both'),
				'required' => false
			)
		);

		$details->addField(
			'status', 'select',
			array(
				'name'  => 'status',
				'label' => $this->__('Status'),
				'options'	=>	array('1' => 'Enable','0' =>'Disable'),
				'required' => false
			)
		);

		$details->addField(
			'username', 'text',
			array(
				'name'  => 'username',
				'label' => $this->__('Username'),
				'required' => false
			)
		);

		$details->addField(
			'password', 'password',
			array(
				'name'  => 'password',
				'label' => $this->__('Password'),
				'required' => false
			)
		);

		if ($id) {
			$form->setValues($formData);
		}
		return parent::_prepareForm();
	}
}
