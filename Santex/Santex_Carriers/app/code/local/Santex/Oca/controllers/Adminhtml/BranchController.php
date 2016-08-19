<?php
/**
 * Santex_Oca
 *
 * @category   Santex
 * @package    Santex_Oca
 * @copyright  Copyright (c) 2015 Santex Group. (http://santexgroup.com/)
 */
class Santex_Oca_Adminhtml_BranchController extends Mage_Adminhtml_Controller_Action {    

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('sales/oca')
            ->_title('OCA')
        ;
        return $this;
    }

    public function indexAction() {
        $this->loadLayout()
            ->_initAction()
            ->_title($this->__('Branches'))
            ->_addContent($this->getLayout()->createBlock('oca/adminhtml_branch'))
            ->renderLayout();
    }

    public function loadAction() {
        try {
            Mage::getModel('oca/epak_webservice')->updateBranches();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('oca')->__('Branches successfuly updated'));
        } catch (Exception $e){
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('oca')->__('An error occurred while updating branches from Oca'));
            Mage::logException($e);
        }
        $this->getResponse()->setRedirect($this->getUrl('oca/adminhtml_branch'));
    }

    public function newAction() {
        die('create new branch');
    }

    public function editAction() {
        var_dump($this->getRequest()->getParam('id'));
        die('edit branch');
    }
}
