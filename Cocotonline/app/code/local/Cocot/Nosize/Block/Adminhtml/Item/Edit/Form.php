<?php

class Cocot_Nosize_Block_Adminhtml_Item_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * Init class
     */
    public function __construct() {
        parent::__construct();

        $this->setId('cocot_nosize_item_form');
        $this->setTitle($this->__('Item Information'));
    }

    /**
     * Setup form fields for inserts/updates
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {
        $model = Mage::registry('cocot_nosize');

        $form = new Varien_Data_Form(array(
                    'id' => 'edit_form',
                    'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                    'method' => 'post'
                ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('nosize')->__('Item Information'),
            'class' => 'fieldset-wide',
                ));

        if ($model->getNosizeId()) {
            $fieldset->addField('nosize_id', 'hidden', array(
                'name' => 'nosize_id',
            ));
        }
        
        $fieldset->addField('date', 'text', array(
            'name' => 'date',
            'label' => Mage::helper('nosize')->__('Date'),
            'title' => Mage::helper('nosize')->__('Date'),
            'required' => true
        ));
        
        $fieldset->addField('email', 'text', array(
            'name' => 'email',
            'label' => Mage::helper('nosize')->__('Email'),
            'title' => Mage::helper('nosize')->__('Email'),
            'required' => true,
        ));
        
        /*$fieldset->addField('gender', 'text', array(
            'name' => 'gender',
            'label' => Mage::helper('nosize')->__('Gender'),
            'title' => Mage::helper('nosize')->__('Gender'),
            'required' => true,
        ));*/
        
        $fieldset->addField('size', 'text', array(
            'name' => 'size',
            'label' => Mage::helper('nosize')->__('Size'),
            'title' => Mage::helper('nosize')->__('Size'),
            'required' => true,
        ));

        $fieldset->addField('product_name', 'text', array(
            'name' => 'product_name',
            'label' => Mage::helper('nosize')->__('Product Name'),
            'title' => Mage::helper('nosize')->__('Product Name'),
            'required' => true,
        ));
        
       /* $fieldset->addField('product_brand', 'text', array(
            'name' => 'product_brand',
            'label' => Mage::helper('nosize')->__('Product Brand'),
            'title' => Mage::helper('nosize')->__('Product Brand'),
            'required' => true,
        ));*/
        
        $fieldset->addField('product_sku', 'text', array(
            'name' => 'product_sku',
            'label' => Mage::helper('nosize')->__('Product Sku'),
            'title' => Mage::helper('nosize')->__('Product Sku'),
            'required' => true,
        ));
        
        $fieldset->addField('url', 'text', array(
            'name' => 'product_url',
            'label' => Mage::helper('nosize')->__('Url'),
            'title' => Mage::helper('nosize')->__('Url'),
            'required' => false,
        ));

        $form->setValues($model->getData());
        if (!$model->getDate()) {
            $form->setValues(array('date' => date('Y-m-d H:i:s')));
        }
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}