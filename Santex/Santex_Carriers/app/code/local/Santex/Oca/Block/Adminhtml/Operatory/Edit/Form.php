<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */

class Santex_Oca_Block_Adminhtml_Operatory_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm() {
        $model = Mage::registry('current_oca_operatory');

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset($this->getFormAction(), array('legend' => Mage::helper('oca')->__('Operatory Details')));

        $fieldset->addField('code', 'text', array(
            'name'      => 'code',
            'class'     => 'validate-number',
            'title'     => Mage::helper('oca')->__('OCA Code'),
            'label'     => Mage::helper('oca')->__('OCA Code'),
            'maxlength' => '10',
            'required'  => true,
        ));

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'title'     => Mage::helper('oca')->__('Method Name'),
            'label'     => Mage::helper('oca')->__('Method Name'),
            'maxlength' => '100',
            'required'  => true,
        ));
        
        $fieldset->addField('uses_idci', 'select', array(
          'label'     => Mage::helper('oca')->__('Uses IDCI'),
          'name'      => 'uses_idci',
          'options'   => array(
              0 => Mage::helper('adminhtml')->__('No'),
              1 => Mage::helper('adminhtml')->__('Yes'),
          )
        ));
        
        $fieldset->addField('pays_on_destination', 'select', array(
          'label'     => Mage::helper('oca')->__('Customer pays on destination'),
          'name'      => 'pays_on_destination',
          'options'   => array(
              0 => Mage::helper('adminhtml')->__('No'),
              1 => Mage::helper('adminhtml')->__('Yes'),
          )
        ));

        $fieldset->addField('active', 'select', array(
            'label'     => Mage::helper('oca')->__('Status'),
            'title'     => Mage::helper('oca')->__('Status'),
            'name'      => 'active',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('oca')->__('Enabled'),
                '0' => Mage::helper('oca')->__('Disabled'),
            ),
        ));

        $form->setAction($this->getUrl('*/*/save'));
        
        if ($model->getId()) {
            $form->addField('operatory_id', 'hidden', array(
                'name' => 'operatory_id',
            ));

            $form->setValues($model->getData());
            $form->setDataObject($model);
        }
        

        $form->setMethod('post');
        $form->setUseContainer(true);

        $form->setId('edit_form');
        $this->setForm($form);
    }


}
