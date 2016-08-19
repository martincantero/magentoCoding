<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Block_Adminhtml_Operatory_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct() {
        parent::__construct();
        $this->setId('ocaOperatoryGrid');
    }

    protected function _prepareCollection() {
        $model = Mage::getModel('oca/operatory');
        $collection = $model->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('operatory_id', array(
            'header'        => Mage::helper('oca')->__('ID'),
            'align'         => 'right',
            'width'         => '50px',
            'filter_index'  => 'operatory_id',
            'index'         => 'operatory_id',
        ));
        
        $this->addColumn('name', array(
            'header'        => Mage::helper('oca')->__('Name'),
            'align'         => 'left',
            'width'         => '150px',
            'filter_index'  => 'name',
            'index'         => 'name',
            'type'          => 'text',
            'truncate'      => 50,
            'escape'        => true,
        ));

                
        $this->addColumn('code', array(
            'header'        => Mage::helper('oca')->__('Code'),
            'align'         => 'left',
            'width'         => '150px',
            'filter_index'  => 'code',
            'index'         => 'code',
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
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('oca')->__('Disabled'),
                1 => Mage::helper('oca')->__('Enabled')
            )
        ));
        
        return parent::_prepareColumns();
    }
    
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id' => $row->getId(),
        ));
    }
}
