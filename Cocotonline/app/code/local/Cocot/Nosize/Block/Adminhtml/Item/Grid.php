<?php

class Cocot_Nosize_Block_Adminhtml_Item_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        // Set some defaults for our grid
        $this->setDefaultSort('nosize_id');
        $this->setId('nosize_item_grid');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }

    protected function _getCollectionClass() {
        // This is the model we are using for the grid
        return 'nosize/item_collection';
    }

    protected function _prepareCollection() {
        // Get and set our collection for the grid
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        // Add the columns that should appear in the grid
        $this->addColumn('nosize_id', array(
            'header' => $this->__('ID'),
            'align' => 'right',
            'width' => '30px',
            'index' => 'nosize_id'
        ));

        $this->addColumn('date', array(
            'header' => $this->__('Fecha Solicitado'),
            'align' => 'center',
            'index' => 'date',
            'type'  => 'date',
            'width' => '130px'
        ));

        $this->addColumn('email', array(
            'header' => $this->__('Email'),
            'index' => 'email'
        ));
  
        /*$this->addColumn('gender', array(
            'header' => $this->__('Gender'),
            'index' => 'gender',
            'width' => '80px'
        ));*/
        
        $this->addColumn('size', array(
            'header' => $this->__('Size'),
            'index' => 'size',
            'width' => '20px'
        ));
        
        $this->addColumn('product_name', array(
            'header' => $this->__('Name'),
            'index' => 'product_name'
        ));
        
        /*$this->addColumn('product_brand', array(
            'header' => $this->__('Brand'),
            'index' => 'product_brand',
            'width' => '120px'
        ));*/
        
        $this->addColumn('product_sku', array(
            'header' => $this->__('Código (configurable)'),
            'index' => 'product_sku',
            'width' => '70px'
        ));
        
        $this->addColumn('product_url', array(
            'header' => $this->__('Url'),
            'index' => 'product_url'
        ));
        
        $this->addColumn('simple_id', array(
            'header' => $this->__('Producto ID'),
            'align' => 'center',
            'index' => 'simple_id',
            'width' => '40px'
        ));
        
        $this->addColumn('sent', array(
            'header' => $this->__('Sent'),
            'align' => 'center',
            'index' => 'sent',
            'width' => '40px',
            'type'  => 'options',
            'options' => array (
                '0' => 'No',
                '1' => 'Si'
            ),
            //'renderer' => 'Cocot_Nosize_Block_Adminhtml_Template_Grid_Renderer_Sent'
        ));

       $this->addColumn('sent_date', array(
            'header' => $this->__('Fecha Notificado'),
            'align' => 'center',
            'index' => 'sent_date',
            'type'  => 'date',
            'width' => '130px'
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('nosize')->__('CSV'));
        
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        // This is where our row data will link to
        return ''; //$this->getUrl('*/*/edit', array('nosize_id' => $row->getId()));
    }

}