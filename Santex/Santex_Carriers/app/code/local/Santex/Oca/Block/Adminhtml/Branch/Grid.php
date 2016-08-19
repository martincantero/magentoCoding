<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Block_Adminhtml_Branch_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct() {
        parent::__construct();
        $this->setId('ocaBranchGrid');
    }

    protected function _prepareCollection() {
        $model = Mage::getModel('oca/branch');
        $collection = $model->getCollection();
        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('code', array(
            'header'        => Mage::helper('oca')->__('ID'),
            'align'         => 'right',
            'width'         => '50px',
            'filter_index'  => 'code',
            'index'         => 'code',
        ));
        
        $this->addColumn('short_name', array(
            'header'        => Mage::helper('oca')->__('Short Name'),
            'align'         => 'left',
            'width'         => '50px',
            'filter_index'  => 'short_name',
            'index'         => 'short_name',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));
                
        $this->addColumn('description', array(
            'header'        => Mage::helper('oca')->__('Description'),
            'align'         => 'left',
            'width'         => '150px',
            'filter_index'  => 'description',
            'index'         => 'description',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));
                        
        $this->addColumn('city', array(
            'header'        => Mage::helper('oca')->__('City'),
            'align'         => 'left',
            'width'         => '150px',
            'filter_index'  => 'city',
            'index'         => 'city',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));

        $this->addColumn('zipcode', array(
            'header'        => Mage::helper('oca')->__('Zipcode'),
            'align'         => 'left',
            'width'         => '50px',
            'index'         => 'zipcode',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));

        $this->addColumn('active', array(
            'header'        => Mage::helper('oca')->__('Status'),
            'align'         => 'left',
            'width'         => '150px',
            'filter_index'  => 'active',
            'index'         => 'active',
            'type'          => 'options',
            'options'       => array(
                0 => Mage::helper('oca')->__('Disabled'),
                1 => Mage::helper('oca')->__('Enabled')
            )
        ));
        
        return parent::_prepareColumns();
    }
    
        
    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array(
            'id' => $row->getId(),
        ));
    }
}
