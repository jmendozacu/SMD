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
 * Time: 11:38
 */
class Silk_Retailer_Block_Adminhtml_Retailers_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
	{
		parent::__construct();
		// Set some defaults for our grid
		$this->setId('id');
		$this->setDefaultSort('id');
		$this->setDefaultDir('desc');
		$this->setSaveParametersInSession(false);
	}

	protected function _prepareCollection()
	{
		$collection = Mage::getModel('silk_retailer/retailer')->getCollection();
		$this->setCollection($collection);
		$this->setSaveParametersInSession(false);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns()
	{
		$this->addColumn('id',
			array(
				'header' => $this->__('ID'),
				'width' => '10px',
				'index' => 'id'
			)
		);

		$this->addColumn('title',
			array(
				'header' => $this->__('Title'),
				'index' => 'title',
			)
		);

		$this->addColumn('telephone',
			array(
				'header' => $this->__('Phone'),
				'index' => 'telephone',
			)
		);

		$this->addColumn('email',
			array(
				'header' => $this->__('Email'),
				'index' => 'email'
			)
		);

		$this->addColumn('status',
			array(
				'header' => $this->__('Status'),
				'type' => 'options',
				'options' => array('1' => 'Enable','0' =>'Disable'),
				'index' => 'status',
				'width' => '50px',
			)
		);

		$this->addColumn('',
			array(
				'header' => $this->__('Action'),
				'filter' =>	false,
				'width' => '20px',
				'renderer' => 'Silk_Retailer_Block_Adminhtml_Retailers_Grid_Renderer_Action'
			)
		);

		return parent::_prepareColumns();
	}

	public function getRowUrl($row)
	{
		// This is where our row data will link to
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('id');
		$this->getMassactionBlock()->setFormFieldName('id');
		// add export excle
		$this->getMassactionBlock()->addItem('export', array(
			'label'=> Mage::helper('catalog')->__('Export'),
			'url'  => $this->getUrl('*/*/exportExcel')
		));
		return $this;
	}
}