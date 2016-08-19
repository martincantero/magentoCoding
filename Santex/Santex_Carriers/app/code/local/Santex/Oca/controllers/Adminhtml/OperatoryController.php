<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */
class Santex_Oca_Adminhtml_OperatoryController extends Mage_Adminhtml_Controller_Action {    
    
    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('sales/oca')
            ->_title($this->__('OCA Operatories'))
        ;
        return $this;
    }

    public function indexAction() {
        $this->loadLayout()
            ->_initAction()
            ->_title($this->__('List'))
            ->_addContent($this->getLayout()->createBlock('oca/adminhtml_operatory'))
            ->renderLayout();
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('oca/operatory');

        if ($id) {
            $this->_title($this->__('Edit'));
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('oca')->__('This operatory no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        } else {
            $this->_title($this->__('Add'));
        }

        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        Mage::register('current_oca_operatory', $model);

        $this->loadLayout()->_initAction()->_addContent($this->getLayout()->createBlock('oca/adminhtml_operatory_edit'));
        $this->getLayout()->getBlock('head')->setCanLoadRulesJs(true);

        $this->renderLayout();
    }

    public function saveAction() {
        $operatoryId = $this->getRequest()->getParam('operatory_id', false);
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('oca/operatory');
            
            if ($operatoryId){
                $model->load($operatoryId);
            }

            $model->addData($data);
            try {
                $query = $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('oca')->__('Operatory successfully saved.'));
                
                $this->getResponse()->setRedirect($this->getUrl('oca/adminhtml_operatory'));
                return;
            } catch (Exception $e){
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirectReferer();
    }
}
