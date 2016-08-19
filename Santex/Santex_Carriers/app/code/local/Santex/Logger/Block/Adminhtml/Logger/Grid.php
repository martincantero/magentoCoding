<?php
/**
 * Santex_Logger
 * 
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * Module based on Dc_Logger.
 *
 * @category   Santex
 * @package    Santex_Logger
 * @copyright  Copyright (c) 2014 Santex Group. (http://santexgroup.com/)
 */

class Santex_Logger_Block_Adminhtml_Logger_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('loggerGrid');
        $this->setDefaultSort('log_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('logger/logs')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('log_id', array(
            'header'    => Mage::helper('logger')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'log_id',
        ));

        $this->addColumn('log_date', array(
            'header'    => Mage::helper('logger')->__('Date'),
            'align'     =>'left',
            'type'      => 'datetime',
            'index'     => 'log_date',
        ));
      
        $this->addColumn('process_name', array(
            'header'    => Mage::helper('logger')->__('Process'),
            'index'     => 'process_name',
            'type'      => 'options',
            'options'   => Mage::getModel('logger/process')->getOptionArray()
        ));
        
        $this->addColumn('message', array(
            'header'    => Mage::helper('logger')->__('Message'),
            'index'     => 'message',
        ));
        
		$this->addExportType('*/*/exportCsv', Mage::helper('logger')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('logger')->__('XML'));
	  
      return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('logger_id');
        $this->getMassactionBlock()->setFormFieldName('logger');
        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('logger')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('logger')->__('Are you sure?')
        ));
        return $this;
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

}
